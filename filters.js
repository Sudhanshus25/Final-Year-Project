function toggleFilter(header) {
  const options = header.nextElementSibling;
  options.style.display = options.style.display === "none" ? "block" : "none";
}

document.querySelectorAll('.filter-options input[type="checkbox"]').forEach(cb => {
  cb.addEventListener('change', filterProducts);
});

function filterProducts() {
  const filters = {};

  // Gather all selected filters
  document.querySelectorAll('.filter-options').forEach(section => {
    const key = section.dataset.key;
    const selected = Array.from(section.querySelectorAll('input[type="checkbox"]:checked'))
                          .map(cb => cb.value);
    if (selected.length > 0) {
      filters[key] = selected;
    }
  });

  // Filter products
  document.querySelectorAll('.product-card').forEach(card => {
    let visible = true;

    for (const key in filters) {
      const attrValue = (card.dataset[key] || "").toLowerCase();
      if (!filters[key].includes(attrValue)) {
        visible = false;
        break;
      }
    }

    card.style.display = visible ? 'block' : 'none';
  });
}
