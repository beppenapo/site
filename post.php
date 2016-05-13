<?php
session_start();
require("inc/db.php");
if(isset($_GET['x'])){$x=0;}else{$x=1;}
$a ="SELECT p.id, p.data, p.titolo, r.utente FROM main.post p, main.usr u, main.rubrica r WHERE p.usr = u.id AND u.rubrica = r.id AND p.pubblica = 1 order by data desc;";
$b = pg_query($connection, $a);
while($c = pg_fetch_array($b)){
    $data = split(" ",$c['data']);
    $data = $data[0];
    $post .= "<tr>";
    $post .= "<td><a href='post_view.php?p=".$c['id']."'><i class='fa fa-arrow-right'></i></a></td>";
    $post .= "<td>".$c['titolo']."</td>";
    $post .= "<td>".$c['utente']."</td>";
    $post .= "<td>".$data."</td>";
    $post .= "</tr>";
}
?>
<!DOCTYPE html>
<html>
  <head>
      <?php require("inc/meta.php"); ?>
      <link href="css/post.css" rel="stylesheet" media="screen" >
      <link href="lib/FooTable/css/footable.core.min.css" rel="stylesheet" media="screen" >
  </head>
  <body>
    <header id="main"><?php require("inc/header.php"); ?></header>
    <div id="mainWrap">
      <section class="content">
        <header>Archivio post</header>
        <section class="toolbar">
            <div class="listTool">
                <?php if(isset($_SESSION["id"])){ ?><a href="post_new.php" title="inserisci un nuovo post"><i class="fa fa-plus"></i>nuovo post</a><?php } ?>
            </div>
            <div class="tableTool">
                <select id="change-page-size">
                    <option disabled selected> righe visibili </option>
    				<option value="30">30</option>
    				<option value="40">40</option>
                    <option value="50">50</option>
                    <option value="60">60</option>
                </select>
                <?php if(isset($_SESSION['id'])){?><label id="statoLabel">bozze</label><?php } ?>
                <input type="search" placeholder="...cerca" id="filtro">
                <i class="fa fa-undo clear-filter" title="Pulisci filtro"></i>
            </div>
        </section>
        <table class="tableList footable toggle-arrow-tiny" data-page-size="20" data-filter="#filtro" data-filter-text-only="true">
            <thead>
                <tr>
                    <th data-sort-ignore="true"></th>
                    <th data-sort-ignore="true">Titolo</th>
                    <th data-hide="phone">Autore</th>
                    <th data-hide="phone">Data</th>
                </tr>
            </thead>
            <tbody><?php echo $post; ?></tbody>
            <tfoot class="hide-if-no-paging">
             <tr>
              <td colspan="4">
               <div class="pagination pagination-centered"></div>
              </td>
             </tr>
            </tfoot>
        </table>
      </section>
    </div>
    <footer><?php require("inc/footer.php"); ?></footer>
    <script src="lib/jquery-1.12.0.min.js"></script>
    <script src="ckeditor/ckeditor.js"></script>
    <script src="ckeditor/adapters/jquery.js"></script>
    <script type="text/javascript" src="lib/FooTable/js/footable.js"></script>
    <script type="text/javascript" src="lib/FooTable/js/footable.sort.js"></script>
    <script type="text/javascript" src="lib/FooTable/js/footable.paginate.js"></script>
    <script type="text/javascript" src="lib/FooTable/js/footable.filter.js"></script>
    <script type="text/javascript" src="lib/FooTable/js/footable.striping.js"></script>

    <script src="script/funzioni.js"></script>
    <script>
        $(document).ready(function(){
            $('#testo').ckeditor();
            $('.footable').footable();
            $('#change-page-size').change(function (e) {
				e.preventDefault();
				var pageSize = $(this).val();
				$('.footable').data('page-size', pageSize);
				$('.footable').trigger('footable_initialized');
			});
            var vis;
            $("#statoLabel").on("click", function(){
                $(this).toggleClass('checkedLabel');
                if($(this).hasClass('checkedLabel')){vis = 0;}else{vis = 1; }
                $.ajax({
                    url: 'inc/post_list.php',
                    type: 'POST',
                    data: {vis:vis},
                    dataType : 'json',
                    success: function(data){
                        $.each(data, function(index, item){
                            var id = item.id;
                            var data = item.data;
                            data = data.split(" ");
                            var titolo = item.titolo;
                            var utente = item.utente;
                            $(".tableList tbody").html("<tr><td><a href='post_view.php?p="+id+"'><i class='fa fa-arrow-right'></i></a></td><td>"+titolo+"</td><td>"+utente+"</td><td>"+data[0]+"</td></tr>");
                        });
                    }
                });
            });
        });
    </script>
  </body>
</html>
