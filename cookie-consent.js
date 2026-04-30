function attachBanner() {
    const HTML_CONTENT = `
		<div id="cookieNotice" class="light display-right" style="display: none; border: 1px solid black;">
			<div id="closeIcon" style="display: none;">
			</div>
			<div class="title-wrap">
				<h4>I use cookie</h4>
			</div>
			<div class="content-wrap">
				<div class="msg-wrap">
					<p>
						My website uses cookies necessary for its basic functioning.
						By continuing browsing, you consent to my use of cookies and other technologies.
					</p>
					<div class="btn-wrap">
						<button class="btn-primary" onclick="acceptCookieConsent();">I understand</button>
					</div>
				</div>
			</div>
		</div>
		`;

    let cookiesBanner = document.createElement("div");
    cookiesBanner.innerHTML = HTML_CONTENT;
    document.body.appendChild(cookiesBanner);

    let banner = document.getElementById("cookieNotice");


    banner.style.position = "fixed";
    banner.style.bottom = "0";
    banner.style.right = "0";
    banner.style.margin = "20px";
    banner.style.padding = "20px";
    banner.style.width = "400px";
    banner.style.height = "auto";
}

// Create cookie
function setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

// Delete cookie
function deleteCookie(cname) {
    const d = new Date();
    d.setTime(d.getTime() + 24 * 60 * 60 * 1000);
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=;" + expires + ";path=/";
}

// Read cookie
function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(";");
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == " ") {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function acceptCookieConsent() {
    deleteCookie("user_cookie_consent");
    setCookie("user_cookie_consent", 1, 30);
    document.getElementById("cookieNotice").style.display = "none";
}

window.onload = function() {

    attachBanner();

    let cookie_consent = getCookie("user_cookie_consent");
    if (cookie_consent != "") {
        document.getElementById("cookieNotice").style.display = "none";
    } else {
        document.getElementById("cookieNotice").style.display = "block";
    }
};