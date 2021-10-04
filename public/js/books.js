// get all genres of book
const Genres = document.querySelectorAll('.genre');

Genres.forEach((genre) => {
	genre.addEventListener('click', () => {

		// remove old active class
		Genres.forEach((genre) => {
			genre.classList.remove('active');
		})

		// set active class to change design
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

			// get content zone
			const content = document.querySelector('main')

			// replace content
			content.innerHTML = data.content;
			
		}).catch(e => alert(e));

	});
});

// search
var searchInput = document.getElementById('searchInput');
searchInput.addEventListener('change', () => {
	// get books according to text in the input
	fetch(Url.pathname + "?title=" + genre.innerHTML + '&ajax=1', {
		headers: {
			'X-Requested-Width': 'XMLHttpRequest'
		}
	}).then(response => 
		response.json()
	).then(data => {

		// get content zone
		const content = document.querySelector('main')

		// replace content
		content.innerHTML = data.content;
		
	}).catch(e => alert(e));

})

