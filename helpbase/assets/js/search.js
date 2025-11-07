document.addEventListener('DOMContentLoaded', function () {
  console.log("✅ DOMContentLoaded：search.js 実行開始");

  const input = document.getElementById('help-search-input');
  const button = document.getElementById('help-search-button');
  const resultsContainer = document.getElementById('search-results');

  if (!input || !button || !resultsContainer) {
    console.log("❌ 要素が取得できませんでした");
    return;
  }

  console.log("✅ 要素取得成功");

  // --- 検索実行関数 ---
  function doSearch() {
    const keyword = input.value.trim();
    console.log("🔍 検索実行 keyword:", keyword);

    if (keyword.length < 2) {
      resultsContainer.innerHTML = '<div class="no-result">2文字以上で入力してください。</div>';
      return;
    }

    // WordPress admin-ajax.php にリクエスト送信
    fetch(`${ajaxurl}?action=help_live_search&keyword=${encodeURIComponent(keyword)}`)
      .then(response => response.json())
      .then(data => {
        console.log("📩 Ajaxレスポンス:", data);
        resultsContainer.innerHTML = data.length
          ? data.map(item => `<div class="result-item"><a href="${item.url}">${item.title}</a></div>`).join('')
          : '<div class="no-result">該当する記事は見つかりませんでした。</div>';
      })
      .catch(error => {
        console.error("⚠️ 検索エラー:", error);
      });
  }

  // --- 入力イベント（リアルタイム検索） ---
  input.addEventListener('input', () => {
    console.log("⌨️ 入力イベント:", input.value);
    if (input.value.trim().length >= 2) {
      doSearch();
    } else {
      resultsContainer.innerHTML = '';
    }
  });

  // --- ボタンクリックイベント ---
  button.addEventListener('click', (e) => {
    console.log("🖱️ ボタンクリックイベント");
    // フォーム送信を止めず、ページ遷移も許可する（予約ルームズ同様）
    // e.preventDefault(); ← これを入れると search.php に遷移しなくなるので注意！
    doSearch();
  });

  // --- タブ切り替え処理（存在する場合） ---
  const tabButtons = document.querySelectorAll('.tab-button');
  const tabContents = document.querySelectorAll('.tab-content');

  if (tabButtons.length && tabContents.length) {
    tabButtons.forEach(button => {
      button.addEventListener('click', () => {
        const target = button.dataset.tab;

        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));

        button.classList.add('active');
        document.getElementById(`tab-${target}`).classList.add('active');
      });
    });
  }

  console.log("✅ search.js 読み込み完了");
});
