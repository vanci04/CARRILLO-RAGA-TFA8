'use strict';
const accountSearch = document.querySelector('#account-search');
if (accountSearch) {
  const rows = [...document.querySelectorAll('#account-rows tr')];
  accountSearch.addEventListener('input', () => {
    const query = accountSearch.value.trim().toLocaleLowerCase();
    let count = 0;
    for (const row of rows) {
      row.hidden = !row.textContent.toLocaleLowerCase().includes(query);
      if (!row.hidden) count++;
    }
    document.querySelector('#no-accounts').hidden = count !== 0;
    document.querySelector('#account-count').textContent = `Showing ${count} of ${rows.length} accounts`;
  });
}
document.querySelector('#print-receipt')?.addEventListener('click', () => window.print());
