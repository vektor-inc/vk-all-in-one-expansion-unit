/**
 * wp-env の tests-cli コンテナ経由で wp-cli を実行する共通ヘルパー。
 *
 * e2e は testsPort (デフォルト 8889 / 本リポジトリのローカル運用では .wp-env.override.json で変更)
 * の テスト用 WordPress を使うため、データ初期化・確認も tests-cli コンテナで行う必要がある。
 *
 * 複数プロジェクトの wp-env を同時に起動していても正しいコンテナだけを掴めるよう、
 * コンテナ名の文字列一致ではなく「このリポジトリ ( wp-env の cwd ) を bind mount している
 * tests-cli サービスのコンテナ」を条件に解決する（詳細は resolveTestsCliContainerId 参照）。
 *
 * 値の受け渡しは `docker exec` に配列で渡した引数をそのまま argv としてコンテナへ渡す
 * ( execFileSync はホスト側のシェルを経由しないため、コマンドライン全体をシェルで
 * 連結し直す `npx wp-env run` と異なり、引用符・スペース・HTML タグを含む値でも安全 )。
 */
import { execFileSync } from 'child_process';
import { realpathSync } from 'fs';

// 解決済み tests-cli コンテナ ID のキャッシュ（同一プロセス内での docker inspect 呼び出しを減らす）。
let cachedContainerId: string | null = null;

/**
 * execFileSync が投げる Error から stdout / stderr を取り出し、判別しやすい1つの
 * メッセージに合成したエラーを作る。
 * execFileSync の Error.message は "Command failed: ..." のような汎用文言のみで、
 * wp-cli 自体が返すエラーメッセージ ( 呼び出し元が文字列判定に使う ) は
 * error.stdout / error.stderr ( Buffer ) 側に入っているため。
 *
 * @param error   execFileSync が投げた例外.
 * @param context どの処理での失敗かを示す短い説明文.
 * @return 合成済みメッセージを持つ Error.
 */
const wrapExecError = ( error: unknown, context: string ): Error => {
	if ( error && typeof error === 'object' && 'stdout' in error ) {
		const execError = error as {
			stdout?: Buffer | string;
			stderr?: Buffer | string;
			message?: string;
		};
		const stdout = execError.stdout ? execError.stdout.toString() : '';
		const stderr = execError.stderr ? execError.stderr.toString() : '';
		return new Error(
			`${ context }: ${ execError.message ?? 'command failed' }\n${ stdout }\n${ stderr }`.trim()
		);
	}
	const message = error instanceof Error ? error.message : String( error );
	return new Error( `${ context }: ${ message }` );
};

/**
 * tests-cli サービスのコンテナ ID を、bind mount のソースパスで一意に特定する。
 *
 * `docker ps --filter name=tests-cli` の先頭1件を使う実装は、他プロジェクトの wp-env が
 * 同時に起動していると別プロジェクトのコンテナを掴んでしまう ( name フィルタは部分一致で、
 * どのプロジェクトの tests-cli にもマッチするため )。
 *
 * ここでは docker-compose 標準ラベル `com.docker.compose.service=tests-cli` を持つ
 * コンテナを列挙し、その中から「このリポジトリのディレクトリ ( wp-env 実行時の cwd ) を
 * bind mount している」ものだけを選ぶ。wp-env は .wp-env.json の "plugins": ["."] で
 * 現在のディレクトリをそのままプラグインとしてマウントするため、この bind mount の
 * Source は常にこのリポジトリの絶対パスと一致し、プロジェクト間で衝突しない。
 *
 * @return 一意に特定できた tests-cli コンテナの ID.
 */
const resolveTestsCliContainerId = (): string => {
	if ( cachedContainerId ) {
		return cachedContainerId;
	}

	// symlink 差異による比較ズレを避けるため realpath で正規化する。
	const pluginDir = realpathSync( process.cwd() );

	let idsRaw: string;
	try {
		idsRaw = execFileSync(
			'docker',
			[
				'ps',
				'--filter',
				'label=com.docker.compose.service=tests-cli',
				'--format',
				'{{.ID}}',
			],
			{ encoding: 'utf-8' }
		).trim();
	} catch ( error ) {
		throw wrapExecError(
			error,
			'resolveTestsCliContainerId: failed to list tests-cli containers via `docker ps`'
		);
	}

	if ( ! idsRaw ) {
		throw new Error(
			'resolveTestsCliContainerId: no tests-cli container is running. Run `npx wp-env start` first.'
		);
	}

	const candidateIds = idsRaw.split( '\n' ).filter( Boolean );

	for ( const id of candidateIds ) {
		let mounts: Array<{ Source?: string }> = [];
		try {
			const mountsRaw = execFileSync(
				'docker',
				[ 'inspect', id, '--format', '{{json .Mounts}}' ],
				{ encoding: 'utf-8' }
			);
			const parsed = JSON.parse( mountsRaw );
			// マウントが一つも無いコンテナでは `.Mounts` が JSON の null になるため、
			// その場合は空配列に確定させる（null のまま .some() を呼ぶと例外で
			// このコンテナだけでなく探索全体が落ちてしまう）。
			mounts = Array.isArray( parsed ) ? parsed : [];
		} catch {
			// このコンテナの検査に失敗しても他候補の探索は続ける（destroy 直後の一時的な不整合等を許容）。
			continue;
		}

		const isOurContainer = mounts.some( ( mount ) => {
			if ( ! mount.Source ) {
				return false;
			}
			// Docker Desktop (macOS) は再起動のタイミング等によって、同じ bind mount でも
			// `docker inspect` が返す Source を `/Users/...` のままの時と、内部 VM 上のパスを
			// 示す `/host_mnt/Users/...` ( 先頭に /host_mnt が付く ) の時とで揺れることがある
			// ( 本 PR のレビュー対応で `.wp-env.json` を変更し `npx wp-env start` を再実行した際に
			// 実機で再現した ) 。ホスト側では `/host_mnt` は存在しないパスなので、比較前に
			// この接頭辞だけを取り除いて正規化する。
			const normalizedSource = mount.Source.replace(
				/^\/host_mnt(?=\/)/,
				''
			);
			try {
				return realpathSync( normalizedSource ) === pluginDir;
			} catch {
				// マウント元がホスト上に無い場合（volume 等）は文字列一致のみで判定する。
				return normalizedSource === pluginDir;
			}
		} );

		if ( isOurContainer ) {
			cachedContainerId = id;
			return id;
		}
	}

	throw new Error(
		`resolveTestsCliContainerId: no tests-cli container mounts ${ pluginDir }. ` +
			`Checked ${ candidateIds.length } candidate container(s) — other wp-env projects may be ` +
			'running concurrently. Make sure `npx wp-env start` was run from this worktree.'
	);
};

/** runWpCli の追加オプション。 */
export type RunWpCliOptions = {
	/** コンテナ内プロセスに渡す追加の環境変数（サニタイズ迂回 mu-plugin の起動フラグ等）。 */
	env?: Record<string, string>;
};

/**
 * tests-cli コンテナで wp-cli コマンドを実行する。
 *
 * 引数はシェルを経由せず argv としてそのまま渡るため、引用符・スペース・HTML タグを
 * 含む値も安全に渡せる（`npx wp-env run` は内部でシェル経由の文字列連結を行うため使えない）。
 *
 * @param args wp コマンドへ渡す引数（例: ['post', 'meta', 'update', '5', 'key', 'value']）.
 * @param options 追加オプション（env 等）.
 * @return コマンドの標準出力（前後の空白は呼び出し元で trim すること）.
 */
export const runWpCli = (
	args: string[],
	options: RunWpCliOptions = {}
): string => {
	const containerId = resolveTestsCliContainerId();

	// 値の受け渡しは常に argv 経由（stdio[0] は 'ignore'）で行うため、標準入力を
	// 使わない。`-i` ( STDIN を開いたままにする ) は不要なので付けない
	// ( 標準入力を使うコマンドは getPostMetaRaw 側で個別に `-i` を付けている )。
	const dockerArgs: string[] = [ 'exec' ];
	if ( options.env ) {
		for ( const [ key, value ] of Object.entries( options.env ) ) {
			dockerArgs.push( '-e', `${ key }=${ value }` );
		}
	}
	dockerArgs.push( containerId, 'wp', ...args, '--path=/var/www/html' );

	try {
		return execFileSync( 'docker', dockerArgs, {
			encoding: 'utf-8',
			// 標準入力は使わないため 'ignore'（値の受け渡しは常に argv で行う）。
			stdio: [ 'ignore', 'pipe', 'pipe' ],
		} );
	} catch ( error ) {
		throw wrapExecError(
			error,
			`runWpCli: failed to run \`wp ${ args.join( ' ' ) }\``
		);
	}
};

/**
 * CTA 画像位置のサニタイズ迂回 mu-plugin ( veu-e2e-bypass-cta-sanitize.php ) を発動させた
 * 状態で wp-cli コマンドを実行する。
 *
 * 「不正値が既に DB に保存されている状態」を作るためだけに使う。mu-plugin 側で
 * `defined('WP_CLI') && WP_CLI` かつこの環境変数の両方を条件にしているため、
 * Web リクエスト（フロント表示・REST 保存）には絶対に影響しない。
 *
 * @param args wp コマンドへ渡す引数.
 * @return コマンドの標準出力.
 */
export const runWpCliBypassingCtaSanitize = ( args: string[] ): string => {
	return runWpCli( args, {
		env: { VEU_E2E_BYPASS_CTA_SANITIZE: '1' },
	} );
};

/**
 * 投稿メタを `wp post meta update` で更新する。
 *
 * 【注意】wp-cli の `post meta update` は、サニタイズ後の値が現在の DB 値と一致すると
 * 「Failed to update custom field」で非ゼロ終了する（生の入力値ではなく実際に書き込む
 * 値と現在値を比較しているため、サニタイズで値が丸められて現在値と同じになった場合も失敗扱いになる）。
 * これは「書き込み不要なだけ」で異常ではないため、このメッセージに限り例外を握りつぶす。
 * それ以外のエラー（コンテナ不在・接続不可等）はそのまま呼び出し元へ伝播させる。
 *
 * 【既知の脆さ】この判定は wp-cli が実際に出す英語のエラー文言 ( 'Failed to update
 * custom field' ) への部分一致に依存している。将来 wp-cli のバージョンアップ等で
 * この文言が変わると握りつぶし判定が効かなくなり、無害なはずの「値不変」ケースが
 * エラーとして呼び出し元に伝播するようになる ( = テストが壊れる ) 。その場合は
 * 実行環境の `wp --version` / `wp post meta update` の実際の出力文言を確認し、
 * この文字列を更新すること。
 *
 * @param postId 投稿 ID.
 * @param key メタキー.
 * @param value 書き込む値.
 * @param options runWpCli に渡す追加オプション（サニタイズ迂回時の env 指定等）.
 */
export const updatePostMetaTolerateNoop = (
	postId: number,
	key: string,
	value: string,
	options: RunWpCliOptions = {}
): void => {
	try {
		runWpCli(
			[ 'post', 'meta', 'update', String( postId ), key, value ],
			options
		);
	} catch ( error ) {
		const message = error instanceof Error ? error.message : String( error );
		if ( ! message.includes( 'Failed to update custom field' ) ) {
			throw error;
		}
		// 「サニタイズ後の値が現在値と一致」で弾かれただけなので無視する。
	}
};

/**
 * 投稿を強制削除する。対象がすでに存在しない場合の失敗は無視する。
 *
 * 【なぜ許容するか】このリポジトリの e2e はローカル実行時 ( `workers` 未指定 ) だと
 * 複数ワーカーで別 spec ファイルが並行実行され得る。`cta.spec.ts` の `resetPosts()` は
 * `post_type=post,cta` の投稿を種別ごと一括削除するため、他ファイルが作業中に使っている
 * 投稿を同じ post_type というだけで巻き込んで削除してしまう可能性がある
 * ( CI は `playwright.config.ts` で `workers: 1` のため直列実行になりこの競合は起きないが、
 * ローカルの並列実行では起こり得る ) 。
 * このテスト自身の後片付け ( 各テストの finally ) が「対象は既に無かった」という
 * 良性の理由で失敗し、実際のアサーション結果を覆い隠して FAIL 扱いにしてしまわないよう、
 * `wp post delete` の「対象が見つからない」旨の警告 ( 非ゼロ終了 ) に限り握りつぶす。
 * それ以外のエラー（コンテナ不在・接続不可等）はそのまま呼び出し元へ伝播させる。
 *
 * @param ids 削除する投稿 ID の配列.
 */
export const deletePostsTolerateMissing = ( ids: number[] ): void => {
	if ( ! ids.length ) {
		return;
	}
	try {
		runWpCli( [ 'post', 'delete', '--force', ...ids.map( String ) ] );
	} catch ( error ) {
		const message = error instanceof Error ? error.message : String( error );
		if ( ! /Failed deleting post/.test( message ) ) {
			throw error;
		}
		// 対象が既に存在しなかっただけなので無視する。
	}
};

/**
 * `get_post_meta( $id, $key, false )` の結果を JSON で取得し、
 * 「メタ行が無い」( [] ) と「空文字で1行存在する」( [""] ) を区別できる形で返す。
 *
 * `wp post meta get` は両者を区別せず非ゼロ終了するため使えない
 * ( 画像位置の "Normal" は空文字が期待値のため、このコマンドで読むと期待通りの結果でも失敗する )。
 *
 * @param postId 投稿 ID.
 * @param key メタキー.
 * @return メタ行の値の配列（行が無ければ空配列）.
 */
export const getPostMetaRaw = ( postId: number, key: string ): string[] => {
	// wp eval-file に渡す一時 PHP。get_post_meta の結果をそのまま JSON で標準出力する。
	// ヒアドキュメントではなく eval-file 用のスクリプト文字列を都度 `wp eval-file -`
	// ( 標準入力からスクリプトを読む形 ) で渡す。ファイルを作らずに済み、後片付けも不要。
	const script =
		'<?php echo json_encode( get_post_meta( (int) $args[0], (string) $args[1], false ) );';
	const containerId = resolveTestsCliContainerId();
	try {
		const output = execFileSync(
			'docker',
			[
				'exec',
				'-i',
				containerId,
				'wp',
				'eval-file',
				'-',
				String( postId ),
				key,
				'--path=/var/www/html',
			],
			{
				encoding: 'utf-8',
				input: script,
				stdio: [ 'pipe', 'pipe', 'pipe' ],
			}
		);
		return JSON.parse( output );
	} catch ( error ) {
		throw wrapExecError(
			error,
			`getPostMetaRaw: failed to read meta '${ key }' on post ${ postId }`
		);
	}
};
