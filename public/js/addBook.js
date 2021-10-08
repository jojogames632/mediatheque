let burgerBtn = document.getElementById('burgerBtn');
burgerBtn.addEventListener('click', () => {
	let alertDiv = document.querySelector('.alert');
	if (alertDiv !== null) {
		alertDiv.remove();
	}
});