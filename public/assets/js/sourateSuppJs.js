let choixSourate = document.getElementById('etat_des_lieux_sourate_supp')
let inputSourate = document.getElementById('etat_des_lieux_sourate_suppInput')
let destinationsourate = document.getElementById('etat_des_lieux_sourateSupp')
let tableauVisualisation = document.getElementById('tableauSourateSupp')
let tableauSourateSupp = [];
let tableauSourateSuppNum = [];
function functionAdd() {
    if (!tableauSourateSupp.includes(choixSourate.options[document.getElementById('etat_des_lieux_sourate_supp').selectedIndex].text) && (choixSourate.options[document.getElementById('etat_des_lieux_sourate_supp').selectedIndex].text) !== 'Ex : 18 - Al Kahf') {
        tableauSourateSupp.push(choixSourate.options[document.getElementById('etat_des_lieux_sourate_supp').selectedIndex].text);
        sourateSupp = document.createElement('div')
        tableauVisualisation.setAttribute('class', 'tableauSourateSupp bg-light row  mx-3 p-2')
        sourateSupp.setAttribute('id', 'sourateSuppView')
        sourateSupp.setAttribute('class', 'bg-secondary rounded text-black text-center row m-1 p-2 sourateSuppView')
        sourateSupp.innerHTML = choixSourate.options[document.getElementById('etat_des_lieux_sourate_supp').selectedIndex].text;
        tableauVisualisation.appendChild(sourateSupp);
    }
    destinationsourate.value = tableauSourateSupp;
}

function functionRaz() {
    tableauSourateSupp = [];
    tableauSourateSuppNum = [];
    destinationsourate.value = [];
    while (tableauVisualisation.firstChild) {
        tableauVisualisation.removeChild(tableauVisualisation.lastChild);
    }
}