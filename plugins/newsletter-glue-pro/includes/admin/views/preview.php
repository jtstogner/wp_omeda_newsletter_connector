<?php
/**
 * Preview.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>
<!doctype html>
<html>
<head>

<title><?php _e( 'Campaign preview - Newsletter Glue', 'newsletter-glue' ); ?></title>
<style type="text/css">
html {
	margin: 0;
	padding: 0;
	font-size: 13px;
}

body {
	font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
	margin: 0;
	padding: 0;
	font-size: 1rem;
}

body * {
	font-size: 1rem;
}

header,
.nglue-bar {
	width: 100%;
    background: #ffffff;
	padding: 0 10%;
	box-sizing: border-box;
	display: flex;
	justify-content: space-between;
	margin-top: 15px;
	margin-bottom: 15px;
	align-items: flex-end;
	color: #1e1e1e;
}

.nglue-share {
	display: flex;
	justify-content: center;
	height: 100%;
	flex-direction: column;
}

.nglue-share > div {
	padding: 0;
	height: 36px;
	display: flex;
	align-items: center;
}

.nglue-share-url {
	display: flex;
	align-items: center;
}

.nglue-share-url-text {
    cursor: default;
    padding: 0 0 10px 0 !important;
    font-size: 13px;
    height: auto !important;
}

.nglue-share-url-input {
	background-color: #ffffff !important;
    border: 1px solid #d2d2d2 !important;
    box-shadow: none !important;
    color: #707070;
    height: 36px;
    line-height: 36px;
    width: 360px;
    border-radius: 4px;
    outline: 0 !important;
    box-sizing: border-box;
    padding: 0 10px !important;
}

.nglue-share-url-copy {
	height: 36px;
    box-sizing: border-box;
    position: relative;
    cursor: pointer;
    background: #007cba;
    box-shadow: none !important;
    color: #fff;
    padding: 0;
    width: 90px;
    outline: none;
    border: none;
	border-radius: 4px;
	margin: 0 5px;
}

.nglue-share-url-copy.nglue-share-alt {
    background: #fff !important;
    color: #007cba !important;
    box-shadow: inset 0 0 0 1px #007cba !important;
}

.nglue-share-url-copy.nglue-share-alt:hover {
    color: #006ba1 !important;
    box-shadow: inset 0 0 0 1px #006ba1 !important;
}

.nglue-share-url-copy,
.nglue-share-url-copy:focus,
.nglue-share-url-copy:active {
	background: #007cba;
}

.nglue-share-url-copy:hover {
	background-color: #006ba1;
}

.nglue-browser {
    border-radius: 4px;
    background: #fff;
    position: absolute;
    top: 135px;
    left: 10%;
    right: 10%;
    bottom: 60px;	
}

.nglue-browser.mobile {
	left: 50%;
	margin-left: -200px;
	width: 381px;
	right: auto;
	box-sizing: border-box;
}

.nglue-browser-header {
    padding: 10px 15px;
    text-align: left;
    line-height: 100%;
    border-radius: 4px 4px 0 0;
    background: #e7e7e7;
    min-height: 36px;
    box-sizing: border-box;
}

.nglue-browser-dots {
    list-style: none;
	margin: 0;
    padding-left: 0;
}

.nglue-browser.mobile .nglue-browser-dots {
	display: none;
}

.nglue-browser-dots li {
    display: inline-block;
    height: 7px;
    width: 7px;
    border-radius: 7px;
    background: #c9c9c9;
    margin-right: 5px;
}

.nglue-browser-content {
    border: 1px solid #e7e7e7;
    border-radius: 0 0 4px 4px;
    height: 100%;
}

iframe {
	border: none;
	display: block;
}

iframe.inactive {
	display: none;
}

.nglue-code-source {
	width: 100%;
	height: 100%;
	opacity: 0;
	position: absolute;
	top: -20000px;
	left: -20000px;
}

.nglue-code-source.active {
	opacity: 1;
	position: relative;
	top: auto;
	left: auto;
}

.nglue-code-source textarea {
	width: 100%;
    padding: 10px;
    box-sizing: border-box;
	font-size: 14px;
	font-family: monospace, monospace;
    color: #666;
    min-height: 100%;
    border: none;
    box-shadow: none;
}

.nglue-toggle {
	display: flex;
	align-items: center;
	justify-content: center;
}

.nglue-toggle ul {
	display: flex;
	margin: 0;
	padding: 0;
	align-items: center;
	list-style: none;
	justify-content: center;
	width: 100%;
	height: 36px;
	border-radius: 4px;
}

.nglue-toggle li {
	flex: 1;
	text-align: center;
	display: flex;
	align-items: center;
	height: 100%;
}

.nglue-toggle a {
	display: flex;
	align-items: center;
	justify-content: center;
	width: 100%;
	height: 100%;
	text-decoration: none;
	color: #007cba;
	margin: 0;
	font-size: 1.1rem;
	padding: 0 15px;
	min-width: 110px;
	box-shadow: inset 0 0 0 1px #007cba;
}

.nglue-toggle a svg {
    width: 18px;
    height: 18px;
    margin: 2px 0.4em 0 0;
}

.nglue-toggle ul li:first-child a {
	border-radius: 4px 0 0 4px;
	position: relative;
	right: -1px;
}

.nglue-toggle ul li:last-child a {
	border-radius: 0 4px 4px 0;
	position: relative;
	left: -1px;
}

.nglue-toggle a:hover {
	color: #006ba1;
	box-shadow: inset 0 0 0 1px #006ba1;
}

.nglue-toggle a.active {
	background: #007cba;
	color: #fff !important;
}

.nglue-bar > div {
	display: flex;
	align-items: center;
}

.nglue-bar-colored {
	display: flex;
    border-radius: 999px;
    width: 20px;
    height: 20px;
    margin: 0 4px 0 10px;
	background: #cc3000;
}

.nglue-bar-red { background: #C12D00; }
.nglue-bar-yellow { background: #F2C300; }
.nglue-bar-green { background: #038955; }

.nglue-bar-help {
	color: #707070;
	margin: 0 0 0 8px;
}

.nglue-bar-help a {
	color: #007cba !important;
	text-decoration: none !important;
}

.nglue-bar-help a:hover {
	color: #006ba1 !important;
	text-decoration: underline !important;
}
</style>

<?php do_action( 'newsletterglue_preview_head', $post_id ); ?>

</head>
<body>

<header>
	<?php do_action( 'newsletterglue_preview_bar', $post_id ); ?>
	<div class="nglue-share">
		<div class="nglue-share-url-text"><?php _e( 'Preview URL', 'newsletter-glue' ); ?></div>
		<div class="nglue-share-url">
			<input type="text" class="nglue-share-url-input" id="nglue-copy-url" readonly value="<?php echo esc_url( $preview_url ); ?>" />
			<input type="button" class="nglue-share-url-copy" id="nglue-copy-button" value="<?php _e( 'Copy link', 'newsletter-glue' ); ?>" data-copied="<?php _e( 'Copied', 'newsletter-glue' ); ?>" data-copy="<?php _e( 'Copy link', 'newsletter-glue' ); ?>" />
			<input type="button" class="nglue-share-url-copy nglue-share-alt" id="nglue-copy-html" value="<?php _e( 'Copy HTML', 'newsletter-glue' ); ?>" data-copied="<?php _e( 'Copied', 'newsletter-glue' ); ?>" data-copy="<?php _e( 'Copy HTML', 'newsletter-glue' ); ?>" />
		</div>
	</div>
	<div class="nglue-toggle">
		<ul>
			<li><a href="#" class="nglue-desktop active" id="nglue-desktop"><svg stroke="currentColor" fill="currentColor" stroke-width="0" version="1.1" viewBox="0 0 16 16" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M14 11v-8c0-0.55-0.45-1-1-1h-10c-0.55 0-1 0.45-1 1v8h-2v3h16v-3h-2zM10 13h-4v-1h4v1zM13 11h-10v-7.998c0.001-0.001 0.001-0.001 0.002-0.002h9.996c0.001 0.001 0.001 0.001 0.002 0.002v7.998z"></path></svg><?php _e( 'Desktop', 'newsletter-glue' ); ?></a></li>
			<li><a href="#" class="nglue-mobile" id="nglue-mobile"><svg stroke="currentColor" fill="currentColor" stroke-width="0" version="1.1" viewBox="0 0 16 16" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M11.5 0h-7c-0.825 0-1.5 0.675-1.5 1.5v13c0 0.825 0.675 1.5 1.5 1.5h7c0.825 0 1.5-0.675 1.5-1.5v-13c0-0.825-0.675-1.5-1.5-1.5zM6 0.75h4v0.5h-4v-0.5zM8 15c-0.552 0-1-0.448-1-1s0.448-1 1-1 1 0.448 1 1-0.448 1-1 1zM12 12h-8v-10h8v10z"></path></svg><?php _e( 'Mobile', 'newsletter-glue' ); ?></a></li>
			<li><a href="#" class="nglue-code" id="nglue-code"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M160 368L32 256l128-112m192 224l128-112-128-112m-48-48l-96 320"></path></svg><?php _e( 'Email HTML', 'newsletter-glue' ); ?></a></li>
		</ul>
	</div>
</header>

<div class="nglue-bar">
	<div>
		<span class="nglue-bar-emp"><?php echo esc_html__( 'Email size:', 'newsletter-glue' ); ?></span> 
		<span class="nglue-bar-colored nglue-bar-<?php echo esc_attr( $size_class ); ?>"></span> <?php echo esc_html( $size ); ?>kb
		<span class="nglue-bar-help">
		<?php
			if ( $size_class === 'green' ) {
				echo sprintf( esc_html__( 'Great job! This email won’t get clipped by Gmail. %s.', 'newsletter-glue' ), '<a href="https://newsletterglue.com/docs/email-size-checker-prevent-gmail-clipping/" target="_blank">' . esc_html__( 'Learn more', 'newsletter-glue' ) . '</a>' );
			}
			if ( $size_class === 'yellow' ) {
				echo sprintf( esc_html__( 'This email might get clipped by Gmail. To check, send a test email and open it in Gmail. Then consider removing content. %s.', 'newsletter-glue' ), '<a href="https://newsletterglue.com/docs/email-size-checker-prevent-gmail-clipping/" target="_blank">' . esc_html__( 'Learn more', 'newsletter-glue' ) . '</a>' );
			}
			if ( $size_class === 'red' ) {
				echo sprintf( esc_html__( 'This email will most likely get clipped by Gmail. To check, send a test email and open it in Gmail. Then remove content. %s.', 'newsletter-glue' ), '<a href="https://newsletterglue.com/docs/email-size-checker-prevent-gmail-clipping/" target="_blank">' . esc_html__( 'Learn more', 'newsletter-glue' ) . '</a>' );
			}
		?>
		</span>
	</div>
</div>

<div class="nglue-browser" id="nglue-browser">
	<div class="nglue-browser-header">
		<ul class="nglue-browser-dots">
			<li></li>
			<li></li>
			<li></li>
		</ul>
	</div>
	<div class="nglue-browser-content">
		<iframe width="100%" height="100%" id="nglue-iframe" src="<?php echo esc_url( $preview_url ); ?>"></iframe>
		<div class="nglue-code-source" id="nglue-source">
			<textarea id="nglue-email-code" readonly><?php echo $content; // phpcs:ignore ?></textarea>
		</div>
	</div>
</div>

<script type="text/javascript">
var desktop = document.getElementById("nglue-desktop");
var mobile  = document.getElementById("nglue-mobile");
var code = document.getElementById("nglue-code");
var browser = document.getElementById("nglue-browser");
desktop.addEventListener("click", function( e ) {
	e.preventDefault();
	browser.className = 'nglue-browser';
	desktop.className = 'nglue-desktop active';
	mobile.className = 'nglue-mobile';
	code.className = 'nglue-code';
	document.getElementById("nglue-source").className = 'nglue-code-source inactive';
	document.getElementById("nglue-iframe").className = 'active';
});

mobile.addEventListener("click", function( e ) {
	e.preventDefault();
	browser.className = 'nglue-browser mobile';
	desktop.className = 'nglue-desktop';
	mobile.className = 'nglue-mobile active';
	code.className = 'nglue-code';
	document.getElementById("nglue-source").className = 'nglue-code-source inactive';
	document.getElementById("nglue-iframe").className = 'active';
});

code.addEventListener("click", function( e ) {
	e.preventDefault();
	browser.className = 'nglue-browser';
	desktop.className = 'nglue-desktop';
	mobile.className = 'nglue-mobile';
	code.className = 'nglue-code active';
	document.getElementById("nglue-source").className = 'nglue-code-source active';
	document.getElementById("nglue-iframe").className = 'inactive';
});

var copyBtn = document.getElementById("nglue-copy-button");
copyBtn.addEventListener("click", function() {
    copyToClipboard(document.getElementById("nglue-copy-url"));
	copyBtn.value = copyBtn.getAttribute( 'data-copied' );
	setTimeout( function() {
		copyBtn.value = copyBtn.getAttribute( 'data-copy' );
	}, 500 );
});

var copyhtmlBtn = document.getElementById("nglue-copy-html");
copyhtmlBtn.addEventListener("click", function() {
    copyToClipboard(document.getElementById("nglue-email-code"));
	copyhtmlBtn.value = copyhtmlBtn.getAttribute( 'data-copied' );
	setTimeout( function() {
		copyhtmlBtn.value = copyhtmlBtn.getAttribute( 'data-copy' );
	}, 500 );
});

function copyToClipboard(elem) {
	  // create hidden text element, if it doesn't already exist
    var targetId = "_hiddenCopyText_";
    var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
    var origSelectionStart, origSelectionEnd;
    if (isInput) {
        // can just use the original source element for the selection and copy
        target = elem;
        origSelectionStart = elem.selectionStart;
        origSelectionEnd = elem.selectionEnd;
    } else {
        // must use a temporary form element for the selection and copy
        target = document.getElementById(targetId);
        if (!target) {
            var target = document.createElement("textarea");
            target.style.position = "absolute";
            target.style.left = "-9999px";
            target.style.top = "0";
            target.id = targetId;
            document.body.appendChild(target);
        }
        target.textContent = elem.textContent;
    }
    // select the content
    var currentFocus = document.activeElement;
    target.focus();
    target.setSelectionRange(0, target.value.length);
    
    // copy the selection
    var succeed;
    try {
    	  succeed = document.execCommand("copy");
    } catch(e) {
        succeed = false;
    }
    // restore original focus
    if (currentFocus && typeof currentFocus.focus === "function") {
        currentFocus.focus();
    }
    
    if (isInput) {
        // restore prior selection
        elem.setSelectionRange(origSelectionStart, origSelectionEnd);
    } else {
        // clear temporary content
        target.textContent = "";
    }
    return succeed;
}
</script>

</body>
</html>
