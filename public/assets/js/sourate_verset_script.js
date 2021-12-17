window.onload = () => {

    let sourate_debut = document.querySelector('#etat_des_lieux_sourate_debut');
    let sourate_fin = document.querySelector('#etat_des_lieux_sourate_fin');
    let jours_debut = document.querySelector('#etat_des_lieux_JoursDeDebut');



    sourate_debut.addEventListener("change", function () {
        let form = this.closest("form");
        console.log(form)
        let data = this.name + "=" + this.value;

        console.log(data)



        fetch(form.action, {
            method: form.getAttribute("method"),
            body: data,
            headers: {
                "Content-Type": "application/x-www-form-urlencoded;charset-utf-8"
            }
        })
            .then(response => response.text())
            .then(html => {

                let content = document.createElement("html");
                content.innerHTML = html;
                console.log(html)

                console.log(content)
                let nouveauSelect = content.querySelector("#etat_des_lieux_sourate_debut_verset_debut");
                document.querySelector("#etat_des_lieux_sourate_debut_verset_debut").replaceWith(nouveauSelect);
                console.log(nouveauSelect)
            })

        fetch(form.action, {
            method: form.getAttribute("method"),
            body: data,
            headers: {
                "Content-Type": "application/x-www-form-urlencoded;charset-utf-8"
            }
        })
            .then(response => response.text())
            .then(html => {
                let content = document.createElement("html");
                content.innerHTML = html;
                let nouveauSelect = content.querySelector("#etat_des_lieux_sourate_debut_verset_fin");
                document.querySelector("#etat_des_lieux_sourate_debut_verset_fin").replaceWith(nouveauSelect);
            })
    })

    sourate_fin.addEventListener("change", function () {
        let form = this.closest("form");
        let data = this.name + "=" + this.value;

        fetch(form.action, {
            method: form.getAttribute("method"),
            body: data,
            headers: {
                "Content-Type": "application/x-www-form-urlencoded;charset-utf-8"
            }
        })
            .then(response => response.text())
            .then(html => {
                let content = document.createElement("html");
                content.innerHTML = html;
                let nouveauSelect = content.querySelector("#etat_des_lieux_sourate_fin_verset_debut");
                document.querySelector("#etat_des_lieux_sourate_fin_verset_debut").replaceWith(nouveauSelect);
            })

        fetch(form.action, {
            method: form.getAttribute("method"),
            body: data,
            headers: {
                "Content-Type": "application/x-www-form-urlencoded;charset-utf-8"
            }
        })
            .then(response => response.text())
            .then(html => {
                let content = document.createElement("html");
                content.innerHTML = html;
                let nouveauSelect = content.querySelector("#etat_des_lieux_sourate_fin_verset_fin");
                document.querySelector("#etat_des_lieux_sourate_fin_verset_fin").replaceWith(nouveauSelect);
            })
    });

}