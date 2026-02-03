document.addEventListener("DOMContentLoaded", function () {
            var userFilter = document.getElementById("user-filter");
            var acquistiFilter = document.getElementById("acquisti-filter");
            var orderToggle = document.getElementById("order-toggle");
            var orderDir = document.getElementById("order-dir");
            var orderMode = "nome";
            var asc = true;

            function filterRows(input, selector, attr) {
                if (!input) {
                    return;
                }
                input.addEventListener("input", function () {
                    var value = input.value.toLowerCase();
                    var rows = document.querySelectorAll(selector);
                    rows.forEach(function (row) {
                        var key = (row.getAttribute(attr) || "").toLowerCase();
                        row.style.display = key.indexOf(value) !== -1 ? "" : "none";
                    });
                });
            }

            function sortUsers() {
                var rows = Array.prototype.slice.call(document.querySelectorAll("[data-utente]"));
                rows.sort(function (a, b) {
                    var keyA = orderMode === "data" ? a.getAttribute("data-data") : a.getAttribute("data-utente");
                    var keyB = orderMode === "data" ? b.getAttribute("data-data") : b.getAttribute("data-utente");
                    if (keyA === keyB) {
                        return 0;
                    }
                    if (asc) {
                        return keyA > keyB ? 1 : -1;
                    }
                    return keyA < keyB ? 1 : -1;
                });
                var tbody = rows.length ? rows[0].parentElement : null;
                if (tbody) {
                    rows.forEach(function (row) { tbody.appendChild(row); });
                }
            }

            if (orderToggle) {
                orderToggle.addEventListener("click", function () {
                    orderMode = orderMode === "nome" ? "data" : "nome";
                    orderToggle.textContent = orderMode === "nome" ? "Ordina per nome" : "Ordina per data";
                    sortUsers();
                });
            }

            if (orderDir) {
                orderDir.addEventListener("click", function () {
                    asc = !asc;
                    orderDir.textContent = asc ? "↑" : "↓";
                    sortUsers();
                });
            }

            filterRows(userFilter, "[data-utente]", "data-utente");
            filterRows(acquistiFilter, "[data-acquisto]", "data-acquisto");
        });