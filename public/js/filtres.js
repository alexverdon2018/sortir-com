document.addEventListener('DOMContentLoaded', function(){
    const siteInput = document.querySelector('#inputGroup_site');
    const siteNameInputVal = document.querySelector('#inputGroup_nom');
    const sortieDateDebutInputVal = document.querySelector('#inputGroup_dateDebut').value;
    const sortieDateFinVal = document.querySelector('#inputGroup_dateFin').value;
    const trs = [...document.querySelector('tbody').children];
    debugger;

    siteNameInputVal.addEventListener('keyup', evt =>{
        const siteNameInputVal = evt.currentTarget.value;
        trs.forEach((tr) => {
            tr.style.display = 'table-row';
            if (siteNameInputVal) {
                tr.children[0].textContent.includes(siteNameInputVal) ?
                    tr.style.display = 'table-row' : tr.style.display = 'none';
            }
        });
    });
}, false);

