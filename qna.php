<?php
include_once "parts/header.php";
?>
<body>
<?php
include_once "parts/nav.php";
?>
  <main>
    <section class="banner">
      <div class="container text-white">
        <h1>Q&A</h1>
      </div>
    </section>
    <section class="container">
          <div class="row">
              <div class="col-100 text-center">
                  <p><strong><em>Elit culpa id mollit irure sit. Ex ut et ea esse culpa officia ea incididunt elit velit veniam qui. Mollit deserunt culpa incididunt laborum commodo in culpa.</em></strong></p>
              </div>
          </div>

          <?php         //interaction with QnA.php
           include_once "classes/QnA.php";
           use QnA;

          $qna = new QnA();
          $qna->insertQnA();
          try {
              $result = $qna->getQnA();
              foreach ($result as $row) { ?>
                  <div class="accordion">
                      <div class="question"><?php echo $row['otazka']; ?></div>
                      <div class="answer"><?php echo $row['odpoved']; ?></div>
                  </div>
          <?php }
          } catch (Exception $e) {
              echo "Chyba" . $e->getMessage();
          }
          ?>

    </section>
  </main>

  <?php
include_once "parts/footer.php"
?>