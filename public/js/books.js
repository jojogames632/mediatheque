const Genres = document.querySelectorAll('.genre');

Genres.forEach((genre) => {
	genre.addEventListener('click', () => {
		Genres.forEach((genre) => {
			genre.classList.remove('active');
		})

		genre.classList.add('active');
				
		const Url = new URL(window.location.href);

		// get books according to genre selectionned
		fetch(Url.pathname + "?genre=" + genre.innerHTML + '&ajax=1', {
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
})

