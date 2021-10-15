const mobileHeaderOff = document.getElementById('mobileHeaderOff');
const mobileHeaderOn = document.getElementById('mobileHeaderOn');
let toggleStatus = 0;

// Switch mobile header
function toggleHeader() {
	if (toggleStatus == 0) {
		// hide off header
		mobileHeaderOff.style.display = 'none';
		// show on header
		mobileHeaderOn.classList.remove('d-none');
		mobileHeaderOn.style.display = 'flex';
		toggleStatus = 1;
	}
	else {
		// show off header
		mobileHeaderOff.style.display = 'flex';
		// hide on header
		mobileHeaderOn.style.display = 'none';
		toggleStatus = 0;
	}
}
