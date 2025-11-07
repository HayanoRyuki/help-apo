document.addEventListener('DOMContentLoaded', () => {
  const tocContainer = document.getElementById('toc-list');
  const headers = document.querySelectorAll('.help-article-body h2, .help-article-body h3');
  
  if (!tocContainer || headers.length === 0) return;

  const list = document.createElement('ul');

  headers.forEach(header => {
    const id = header.id || header.textContent.trim().replace(/\s+/g, '-').toLowerCase();
    header.id = id;

    const li = document.createElement('li');
    li.innerHTML = `<a href="#${id}">${header.textContent}</a>`;
    list.appendChild(li);
  });

  tocContainer.appendChild(list);
});
