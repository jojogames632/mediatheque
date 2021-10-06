let burgerBtn = document.getElementById('burgerBtn');
burgerBtn.addEventListener('click', () => {
	var alertDiv = document.querySelector('.alert');
	if (alertDiv !== null) {
		alertDiv.remove();
	}
});