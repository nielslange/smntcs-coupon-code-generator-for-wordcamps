function copyToClipboard() {
	var copyText = document.getElementById("coupon_codes");
	copyText.style.display = "block";
	copyText.select();
	document.execCommand("copy");
	copyText.style.display = "none";
	alert("Copied to clipboard");
}
