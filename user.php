<?php
session_start();
include "config.php";

$kode = $_SESSION['kode_chat'] ?? '';

if (!$kode) {
  exit("Chat tidak ditemukan");
}

/* kirim pesan */
if (isset($_POST['kirim'])) {

  $pesan = $_POST['pesan'];

  mysqli_query($conn, "
  INSERT INTO chat(kode_chat,pengirim,pesan,tipe,waktu,dibaca)
  VALUES('$kode','user','$pesan','text',NOW(),0)
  ");

  exit;
}

/* load chat */
if (isset($_GET['load'])) {

  $data = mysqli_query($conn, "
  SELECT * FROM chat
  WHERE kode_chat='$kode'
  ORDER BY waktu ASC
  ");

  while ($c = mysqli_fetch_assoc($data)) {

    $class = $c['pengirim'] == 'user' ? 'user' : 'admin';

    echo "<div class='bubble $class'>" . $c['pesan'] . "</div>";
  }

  exit;
}
?>

<h2>Chat Admin</h2>

<div id="chatBox" class="chat-box"></div>

<form id="formChat">

  <input name="pesan" placeholder="Tulis pesan">

  <button>Kirim</button>

</form>

<script>

  function loadChat() {

    fetch("chat.php?load=1")
      .then(r => r.text())
      .then(d => {
        document.getElementById("chatBox").innerHTML = d;
      });

  }

  setInterval(loadChat, 2000);

  loadChat();


  document.getElementById("formChat").addEventListener("submit", function (e) {

    e.preventDefault();

    let formData = new FormData(this);

    formData.append("kirim", 1);

    fetch("chat.php", { method: "POST", body: formData })
      .then(() => {
        this.reset();
        loadChat();
      });

  });

</script>