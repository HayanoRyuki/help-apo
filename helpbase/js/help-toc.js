document.addEventListener('DOMContentLoaded', function () {
  const contentArea = document.querySelector('#post-content');
  const tocList = document.querySelector('#toc-list');
  const tocBox = document.querySelector('.p-toc'); // ← 目次枠（スクロールボックス）
  const footer = document.querySelector('footer'); // ← フッター要素

  if (!contentArea || !tocList || !tocBox || !footer) return;

  const headings = contentArea.querySelectorAll('h2, h3');
  if (headings.length === 0) return;

  // ===============================
  // ▼ 目次リストを作成
  // ===============================
  const ul = document.createElement('ul');

  headings.forEach((heading, index) => {
    const tagName = heading.tagName.toLowerCase();
    const text = heading.textContent.trim();
    const id = heading.id || `heading-${index}`;

    if (!heading.id) heading.id = id;

    const li = document.createElement('li');
    li.className = tagName === 'h2' ? 'toc-h2' : 'toc-h3';

    const a = document.createElement('a');
    a.href = `#${id}`;
    a.textContent = text;

    li.appendChild(a);
    ul.appendChild(li);
  });

  tocList.appendChild(ul);

  // ===============================
  // ▼ スクロール誘導ラベル制御
  // ===============================
  const checkScrollStatus = () => {
    const isScrollable = tocList.scrollHeight > tocBox.clientHeight;
    const hasScrolled = tocBox.scrollTop > 10;

    if (isScrollable && hasScrolled) {
      tocBox.classList.add('hide-scroll-label');
    } else if (isScrollable) {
      tocBox.classList.remove('hide-scroll-label');
    } else {
      tocBox.classList.add('hide-scroll-label');
    }
  };

  checkScrollStatus();
  tocBox.addEventListener('scroll', checkScrollStatus);
  window.addEventListener('resize', checkScrollStatus);

  // ===============================
  // ▼ フッターと重なり防止制御（IntersectionObserver）
  // ===============================
  const originalLeft = tocBox.style.left || '40px';
  const originalWidth = tocBox.offsetWidth + 'px';

  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        tocBox.style.position = 'absolute';
        tocBox.style.top = (footer.offsetTop - tocBox.offsetHeight - 40) + 'px';
        tocBox.style.left = originalLeft;
        tocBox.style.width = originalWidth;
      } else {
        tocBox.style.position = 'fixed';
        tocBox.style.top = '120px';
        tocBox.style.left = originalLeft;
        tocBox.style.width = originalWidth;
      }
    });
  });

  observer.observe(footer);
});

// タブ切り替え
document.addEventListener("DOMContentLoaded", function () {
  const tabs = document.querySelectorAll(".rcp-tab");
  const contents = document.querySelectorAll(".rcp-tab-content");

  tabs.forEach(tab => {
    tab.addEventListener("click", function () {
      const target = this.dataset.tab;

      // タブ切り替え
      tabs.forEach(t => t.classList.remove("active"));
      this.classList.add("active");

      // コンテンツ切り替え
      contents.forEach(c => {
        if (c.id === target) {
          c.classList.add("active");
        } else {
          c.classList.remove("active");
        }
      });
    });
  });
});
