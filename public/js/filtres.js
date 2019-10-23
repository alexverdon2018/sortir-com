  document.addEventListener('DOMContentLoaded', function(){
        const siteInput = document.querySelector('#inputGroup_site');
        const siteNameInputVal = document.querySelector('#inputGroup_nom');
        const siteNameInput = document.querySelector('#inputGroup_nom');
        const sortieDateDebutInputVal = document.querySelector('#inputGroup_dateDebut').value;
        const sortieDateFinVal = document.querySelector('#inputGroup_dateFin').value;
        const jeSuisOrgaCheckbox = document.querySelector('#checkbox_jeSuisOrga');
        const jeSuisinscritCheckbox = document.querySelector("#checkbox_jeSuisInsc");
        const trs = [...document.querySelector('tbody').children];
        debugger;
        // Valeur initiale de filteredTrs
        let filteredTrs = [...trs];

        siteNameInput.addEventListener('keyup', evt => {
            const siteNameInputVal = evt.currentTarget.value;
            trs.forEach((tr) => {
                filteredTrs = filteredTrs.map((tr) => {
                    if (siteNameInputVal) {
                        tr.children[0].textContent.includes(siteNameInputVal) ?
                            tr.style.display = 'table-row' : tr.style.display = 'none';
                    }
                    return tr;
                });
            });
        });

      jeSuisOrgaCheckbox.addEventListener('change', evt => {
          debugger;
          filteredTrs = filteredTrs.map((tr) => {

              const showSelfOrga = evt.currentTarget.checked;
              const userName = document.querySelector("#orga_full_name").textContent;
              const trOrga = tr.children[6].textContent;

              if (showSelfOrga && (userName === trOrga)) {
                  tr.style.display = 'table-row';
              } else if (showSelfOrga && (userName !== trOrga)) {
                  tr.style.display = 'none';
              };
              debugger;
              return tr;
          });
      });

      jeSuisinscritCheckbox.addEventListener('change', evt => {
          filteredTrs = filteredTrs.map((tr) => {
              tr.style.display = 'table-row';
              const showSelfRegistered = evt.currentTarget.checked;
              debugger;
          });
      });
      //document.querySelector('tbody').children = filteredTrs;
}, false);
