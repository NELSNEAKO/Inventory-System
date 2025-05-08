document.addEventListener('DOMContentLoaded', () => {
  const tabButtons = document.querySelectorAll('.tab-btn');
  const contentContainer = document.getElementById('tab-content-container');

  const loadTab = async (tabName) => {
    try {
      const response = await fetch(`tabs/${tabName}.php`);
      if (!response.ok) throw new Error("Failed to fetch tab content.");
      
      const html = await response.text();
      contentContainer.innerHTML = html;

      // Handle script injection based on tab name
      switch (tabName) {
        case "dashboard":
          loadScript("js/dashboard.js", () => {
            if (typeof loadDashboardData === "function") {
              loadDashboardData();
            }
          });
          break;
          case "inventory":
            loadScript("js/inventory.js", () => {
              (async () => {
                 fetchInventoryItems();
              })();
            });
            break;
          
        case "users":
          loadScript("js/users.js");
          break;

        case "settings":
          loadScript("js/settings.js");
          break;

        // Add other tabs here...
      }

    } catch (error) {
      contentContainer.innerHTML = `<p style="color:red;">Failed to load <strong>${tabName}</strong> content.</p>`;
      console.error(error);
    }
  };

  // Helper function to dynamically inject scripts
  function loadScript(src, onload) {
    const existing = document.querySelector(`script[src="${src}"]`);
    if (existing) {
      // ✅ Even if script exists, still call the onload callback
      onload?.();
      return;
    }
  
    const script = document.createElement("script");
    script.src = src;
    script.onload = onload;
    script.onerror = () => console.error(`Failed to load script: ${src}`);
    document.body.appendChild(script);
  }
  

  // Handle tab button click
  tabButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelector('.tab-btn.active')?.classList.remove('active');
      btn.classList.add('active');

      const tabName = btn.getAttribute('data-tab');
      loadTab(tabName);
    });
  });

  // Load default tab on page load
  const defaultTab = document.querySelector('.tab-btn.active')?.getAttribute('data-tab') || "dashboard";
  loadTab(defaultTab);
});
