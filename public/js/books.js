window.onload = getBooks();

async function getBooks() {
	let response = await fetch('/data/books');
	var books = await response.json();
	return books;
}

const Genres = document.querySelectorAll('.genre');
const Params = URLSearchParams();

Genres.forEach((value, key) => {
	Params.append(key, value);
});

const Url = new URL(window.location.href);

fetch(Url.pathname + "?" +  Params.toString() + 'ajax=1', {
	headers: {
		'X-Requested-Width': 'XMLHttpRequest'
	}
}).then(response => 
	response.json()
).then(data => {

	// get content zone
	const content = document.querySelector('')

	// replace content
	content.innerHTML = data.content;
	
}).catch(e => alert(e));