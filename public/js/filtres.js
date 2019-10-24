  document.addEventListener('DOMContentLoaded', function(){
        const siteInput = document.querySelector('#inputGroup_site');
        const siteNameInputVal = document.querySelector('#inputGroup_nom');
        const siteNameInput = document.querySelector('#inputGroup_nom');
        const sortieDateDebutInput = document.querySelector('#inputGroup_dateDebut');
        const sortieDateFinInput = document.querySelector('#inputGroup_dateFin');
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
                    tr.style.display = 'table-row';
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
              tr.style.display = 'table-row';
              const showSelfOrga = evt.currentTarget.checked;
              const userName = document.querySelector("#orga_full_name").textContent;
              const trOrga = tr.children[6].textContent;

              if (showSelfOrga && (userName === trOrga)) {
                  tr.style.display = 'table-row';
              } else if (showSelfOrga && (userName !== trOrga)) {
                  tr.style.display = 'none';
              };
              return tr;
          });
      });

      jeSuisinscritCheckbox.addEventListener('change', evt => {
          filteredTrs = filteredTrs.map((tr) => {
              tr.style.display = 'table-row';
              const showSelfRegistered = evt.currentTarget.checked;

              if (showSelfRegistered && (tr.children[7].children[1].value == 1)){
                  tr.style.display = 'table-row';
              } else if (!showSelfRegistered){
                  tr.style.display = 'table-row';
              } else {
                  tr.style.display = 'none';
              }
              return tr;
          });
      });


      sortieDateDebutInput.onchange = evt => {
          const isValidDate = d => {
              return d instanceof Date && !isNaN(d);
          };
          const dateDebutVal =  new Date(sortieDateDebutInput.value);
          const dateFinVal = new Date(sortieDateFinInput.value);
          if (isValidDate(dateDebutVal) && isValidDate(dateFinVal)) {
              debugger;
              filteredTrs = filteredTrs.map((tr) => {
                  debugger;
                  tr.style.display = 'table-row';
                  const rawDate = tr.children[1].textContent.split(" ")[0];
                  const splittedDate = rawDate.split('/');
                  const formattedDate = `${splittedDate[2]}-${splittedDate[1]}-${splittedDate[0]}`;
                  const dateFromTr =  new Date(formattedDate);

                  if (dateFromTr.getTime() >= dateDebutVal.getTime() && dateFromTr.getTime() <= dateFinVal.getTime()) {
                      tr.style.display = 'table-row';
                  } else {
                      tr.style.display = 'none';
                  }
                  return tr;
              });
          } else {
              filteredTrs = filteredTrs.map((tr) => {
                  tr.style.display = 'table-row';
                  return tr;
              });
          }
      };

      sortieDateFinInput.onchange = evt => {
          debugger;
          triggerEvent(sortieDateDebutInput, "change");
      };

      const triggerEvent = (el, type) => {
          if ('createEvent' in document) {
              // modern browsers, IE9+
              const e = document.createEvent('HTMLEvents');
              e.initEvent(type, false, true);
              el.dispatchEvent(e);
          }
      }
      //document.querySelector('tbody').children = filteredTrs;
}, false);
