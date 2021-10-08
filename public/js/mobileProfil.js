let toggleStatus = 0;

// Switch mobile header
function toggleHeader() {
	if (toggleStatus == 0) {
		// hide off header
		document.getElementById('mobileHeaderOff').style.display = 'none';
		// show on header
		document.getElementById('mobileHeaderOn').classList.remove('d-none');
		document.getElementById('mobileHeaderOn').style.display = 'flex';
		toggleStatus = 1;
	}
	else {
		// show off header
		document.getElementById('mobileHeaderOff').style.display = 'flex';
		// hide on header
		document.getElementById('mobileHeaderOn').style.display = 'none';
		toggleStatus = 0;
	}
}
