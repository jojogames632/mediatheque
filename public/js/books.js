const Genres = document.querySelectorAll('.genre');
var currentGenre = '';

Genres.forEach((genre) => {
	genre.addEventListener('click', () => {
		// unset when clicking an active filter
		if (genre.classList.contains('active')) {
			genre.classList.remove('active');
			currentGenre = '';
		}
		else {
			Genres.forEach((genre) => {
				genre.classList.remove('active');
			})

			genre.classList.add('active');
			currentGenre = genre.innerHTML;
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

var searchInput = document.getElementById('searchInput');

searchInput.addEventListener('input', (event) => {
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

