// assets/js/help-tabs.js
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".rcp-tab-container").forEach(container => {
    const tabs = container.querySelectorAll(".rcp-tab");
    const panels = container.querySelectorAll(".rcp-tab-content");

    // 初期アクセシビリティ属性
    tabs.forEach((tab, i) => {
      tab.setAttribute("role", "tab");
      tab.setAttribute("tabindex", tab.classList.contains("active") ? "0" : "-1");
      tab.setAttribute("aria-selected", tab.classList.contains("active") ? "true" : "false");
      tab.setAttribute("aria-controls", tab.dataset.tab);
      if (!tab.id) tab.id = `rcp-tab-${i}`;
    });
    panels.forEach(panel => {
      panel.setAttribute("role", "tabpanel");
      panel.setAttribute("aria-labelledby", [...tabs].find(t => t.dataset.tab === panel.id)?.id || "");
      panel.hidden = !panel.classList.contains("active");
    });

    // クリック/Enter/Spaceで切替
    container.addEventListener("click", e => {
      const tab = e.target.closest(".rcp-tab");
      if (!tab) return;
      activate(tab);
    });
    container.addEventListener("keydown", e => {
      const current = container.querySelector(".rcp-tab.active");
      if (!current) return;
      const tabList = [...tabs];
      const idx = tabList.indexOf(current);
      if (e.key === "ArrowRight" || e.key === "ArrowDown") {
        e.preventDefault();
        activate(tabList[(idx + 1) % tabList.length]).focus();
      } else if (e.key === "ArrowLeft" || e.key === "ArrowUp") {
        e.preventDefault();
        activate(tabList[(idx - 1 + tabList.length) % tabList.length]).focus();
      } else if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        activate(current);
      }
    });

    function activate(targetTab) {
      // タブのactive切替
      tabs.forEach(t => {
        const on = t === targetTab;
        t.classList.toggle("active", on);
        t.setAttribute("aria-selected", on ? "true" : "false");
        t.setAttribute("tabindex", on ? "0" : "-1");
      });
      // パネルの表示切替
      panels.forEach(p => {
        const on = p.id === targetTab.dataset.tab;
        p.classList.toggle("active", on);
        p.hidden = !on;
      });
    }
  });
});
