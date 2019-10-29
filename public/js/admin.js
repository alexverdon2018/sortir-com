document.addEventListener('DOMContentLoaded', () =>{
    const userSearchBar = document.querySelector("#searchUser");
    const villeSearchBar = document.querySelector("#searchVille");

    userSearchBar.addEventListener("keyup", evt => {
        const { children } = document.querySelector("#v-pills-tab");
        const tabsArr = [...children];
        const activeTab = tabsArr.filter((tab) => {
            return tab.classList.contains("active");
        })[0];
        const { value } = evt.currentTarget;
        const { children: rowHTMLElements } = document.querySelector("tbody");
        const rowArr = [...rowHTMLElements];
        if (activeTab.textContent === "Utilisateurs") {
            rowArr.forEach( row => {
               row.style.display = 'table-row';
               const rowUserVal = row.children[0].innerText;
               rowUserVal.toLowerCase().includes(value.toLowerCase())
                   ? row.style.display = 'table-row'
                   : row.style.display = 'none';
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
        const { children: rowHTMLElements } = document.querySelector("tbody");
        const rowArr = [...rowHTMLElements];
        if (activeTab.textContent === "Villes") {
            rowArr.forEach( row => {
                debugger;
                row.style.display = 'table-row';
                const rowUserVal = row.children[0].innerText;
                rowUserVal.toLowerCase().includes(value.toLowerCase())
                    ? row.style.display = 'table-row'
                    : row.style.display = 'none';
            });
        }
    });
}, false);
