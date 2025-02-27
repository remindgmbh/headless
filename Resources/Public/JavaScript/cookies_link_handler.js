import LinkBrowser from "@typo3/backend/link-browser.js";

class CookiesLinkHandler {
    constructor() {
        var form_el = document.getElementById("lcookiesform");
        form_el.addEventListener("submit", function(event) {
            event.preventDefault();
            var value = document.getElementById('lcookies').value;
            LinkBrowser.finalizeFunction('t3://cookies?action=' + value);
        });
    }
}

export default new CookiesLinkHandler();
