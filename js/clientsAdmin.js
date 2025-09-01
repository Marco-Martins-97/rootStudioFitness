function loadApplications(){
    $.post('includes/loadServerData.inc.php', {action: 'loadClientApplications'}, function(response){
        console.log(response);

        if (!response || typeof response !== 'object') {
            console.error('Invalid JSON response:', response);
            $('.applications-container').html('Ocurreu Um Erro, Não Foi Possivel Carregar as Inscrições!');
            return;
        }

        if (response.status === 'error') {
            console.warn('Server error:', response.message || 'Unknown error');
            $('.applications-container').html('Ocurreu Um Erro, Não Foi Possivel Carregar as Inscrições!');
            return;
        }

        let HTMLcontent = '';
        const applications = response.data;

        applications.forEach(application => {
            HTMLcontent += `
                <div class="application-container">
                    <h3>${application.fullName}</h3>
                    <p><strong>Status:</strong> ${application.status}</p>
                    <p><strong>Submitted:</strong> ${application.submissionDate}</p>
            `;
            if(application.status === 'pending'){
                HTMLcontent += `
                        <button id="accept-btn" data-id="${application.applicationId}">Aceitar</button>
                        <button id="reject-btn" data-id="${application.applicationId}">Recusar</button>
                `;
            }
            HTMLcontent += `
                </div>
            `;
        });

        $('.applications-container').html(HTMLcontent);

    }, 'json').fail(function () {
        $('.applications-container').html('Ocurreu Um Erro, Não Foi Possivel Carregar as Inscrições!');
    });
}


$(document).ready(function(){
    loadApplications();

   /*  $(document).on('click', '#accept-btn', function() {
        const applicationId = $(this).data('id');
        reviewApplication(applicationId, 'accepted');
        // loadApplications();
    });

    $(document).on('click', '#reject-btn', function() {
        const applicationId = $(this).data('id');
        reviewApplication(applicationId, 'rejected');
        // loadApplications();
    }); */

});