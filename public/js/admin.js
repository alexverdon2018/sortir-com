document.addEventListener('DOMContentLoaded', () =>{
    const userSearchBar = document.querySelector("#searchUser");
    const villeSearchBar = document.querySelector("#searchVille");
    const siteSearchBar = document.querySelector("#searchSite");

    userSearchBar.addEventListener("keyup", evt => {
        const { children } = document.querySelector("#v-pills-tab");
        const tabsArr = [...children];
        const activeTab = tabsArr.filter((tab) => {
            return tab.classList.contains("active");
        })[0];
        const { value } = evt.currentTarget;
        const { children: rowHTMLElements } = document.querySelector("#v-pills-utilisateurs  tbody");
        const rowArr = [...rowHTMLElements];
        if (activeTab.textContent === "Utilisateurs") {
            rowArr.forEach( row => {
                row.style.display = 'table-row';
                const rowUserPseudo = row.children[0].innerText;
                const rowUserPrenomNom = row.children[1].innerText;
                const rowUserMail = row.children[2].innerText;
                if (rowUserPseudo.toLowerCase().includes(value.toLowerCase()) || rowUserPrenomNom.toLowerCase().includes(value.toLowerCase()) || rowUserMail.toLowerCase().includes(value.toLowerCase())) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    });

    villeSearchBar.addEventListener("keyup", evt => {
        const { children } = document.querySelector("#v-pills-tab");
        const tabsArr = [...children];
        const activeTab = tabsArr.filter((tab) => {
            return tab.classList.contains("active");
        })[0];
        const { value } = evt.currentTarget;
        const { children: rowHTMLElements } = document.querySelector("#v-pills-villes  tbody");
        const rowArr = [...rowHTMLElements];
        if (activeTab.textContent === "Villes") {
            rowArr.forEach( row => {
                row.style.display = 'table-row';
                const rowUserVal = row.children[0].innerText;
                rowUserVal.toLowerCase().includes(value.toLowerCase())
                    ? row.style.display = 'table-row'
                    : row.style.display = 'none';
            });
        }
    });

    siteSearchBar.addEventListener("keyup", evt => {
        const { children } = document.querySelector("#v-pills-tab");
        const tabsArr = [...children];
        const activeTab = tabsArr.filter((tab) => {
            return tab.classList.contains("active");
        })[0];
        const { value } = evt.currentTarget;
        const { children: rowHTMLElements } = document.querySelector("#v-pills-sites  tbody");
        const rowArr = [...rowHTMLElements];
        if (activeTab.textContent === "Sites") {
            rowArr.forEach( row => {
                row.style.display = 'table-row';
                const rowUserVal = row.children[0].innerText;
                rowUserVal.toLowerCase().includes(value.toLowerCase())
                    ? row.style.display = 'table-row'
                    : row.style.display = 'none';
            });
        }
    });


}, false);