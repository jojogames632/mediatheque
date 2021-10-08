const Genres = document.querySelectorAll('.genre');
const paginationContainer = document.getElementById('paginationContainer');
let searchInput = document.getElementById('searchInput');
let currentGenre = '';

Genres.forEach((genre) => {
	genre.addEventListener('click', () => {
		// unset when clicking an active filter
		if (genre.classList.contains('active')) {
			genre.classList.remove('active');
			currentGenre = '';
			// show pagination
			paginationContainer.classList.add('d-flex');
		}
		else {
			Genres.forEach((genre) => {
				genre.classList.remove('active');
			})

			genre.classList.add('active');
			currentGenre = genre.innerHTML;
			searchInput.value = '';		
			// hide pagination (not working with async filters)
			paginationContainer.classList.remove('d-flex');
			paginationContainer.style.display = 'none';
		}

		const Url = new URL(window.location.href);

		// get books according to genre selectionned
		fetch(Url.pathname + "?genre=" + currentGenre + '&ajax=1', {
			headers: {
				'X-Requested-Width': 'XMLHttpRequest'
			}
		}).then(response => 
			response.json()
		).then(data => {
			const content = document.querySelector('main')
			content.innerHTML = data.content;
		}).catch(e => alert(e));
	});
});

searchInput.addEventListener('input', (event) => {

	if (event.target.value == '' & currentGenre == '') {
		// show pagination if no input & filter
		paginationContainer.classList.add('d-flex');
	}
	else {
		// hide pagination (not working with async filters)
		paginationContainer.classList.remove('d-flex');
		paginationContainer.style.display = 'none';
	}
	
	const Url = new URL(window.location.href);

	if (currentGenre != null) {
		// get books by title + genre
		fetch(Url.pathname + "?genre=" + currentGenre + "&title=" + event.target.value + '&ajax=1',  {
			headers: {
				'X-Requested-Width': 'XMLHttpRequest'
			}
		}).then(response => 
			response.json()
		).then(data => {
			const content = document.querySelector('main')
			content.innerHTML = data.content;
		}).catch(e => alert(e));
	}
	else {
		// get books by title only
		fetch(Url.pathname + "?title=" + event.target.value + '&ajax=1',  {
			headers: {
				'X-Requested-Width': 'XMLHttpRequest'
			}
		}).then(response => 
			response.json()
		).then(data => {
			const content = document.querySelector('main')
			content.innerHTML = data.content;
		}).catch(e => alert(e));
	}
})

