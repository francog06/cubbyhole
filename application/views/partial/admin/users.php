<div class="site-wrapper">
    <div class="">
        <div class="cover-container">
            <!-- Menu -->
            <div class="masthead clearfix">
                <div class="">
                    <h3 class="masthead-brand"><img src="<?=img("logo.png")?>" height="60" alt="logo" /></h3>
                    <ul class="nav nav-pills pull-right" style="margin-top:20px;">
                        <li class="active"><a href="/admin/index">Accueil</a></li>
                        <li><a href="/home/price">Users</a></li>
                        <li><a href="/home/download">Plans</a></li>
                    </ul>
                </div>
            </div>

            <div class="inner cover admin">
               <h1>Gestion utilisateurs</h1>
               <table class="table table-striped">
                <tr>
                    <th>#</th>
                    <th>Email</th>
                    <th>Plan</th>
                    <th>Admin ?</th>
                    <th>Actions</th>
                </tr>

               <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user["id"]; ?></td>
                    <td><?php echo $user["email"]; ?></td>
                    <td><?php echo "hello" ?></td>
                    <td><?php echo $user["is_admin"]==true?"Oui":"Non"; ?></td>
                    <td id="<?php echo $user["id"]; ?>"><a class="editer">Editer</a> - <a class="supprimer">Supprimer</a></td>
                </tr>
               <?php endforeach; ?>
               </table>

               <div class="result"></div>
            </div>

            <div class="mastfoot">
                <div class="inner">
                    <p>Cubbyhole powered baby !</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $("a.supprimer").click(function(){
        var userId = $(this).parent().attr('id');
        $.ajax({
            url: '/api/user/delete/'+userId,
            type: 'DELETE',
            headers:{
                "X-API-KEY":"5422e102a743fd70a22ee4ff7c2ebbe8"
            },
            success: function(result) {
                if(result.error == false){
                    $("div.result").append('<p class="bg-success" style="padding: 5px 0px;">'+result["message"]+'</p>');
                }
                else{
                    $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">'+result["message"]+'</p>');
                }
            },
            error: function(result) {
                    $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">Erreur lors du delete.</p>');
                
            }
        });
    });
    
</script>