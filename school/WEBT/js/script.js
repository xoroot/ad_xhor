function w3_open() {
    document.getElementById("side-nav").style.display = "block";
}

function w3_close() {
    document.getElementById("side-nav").style.display = "none";
}

/*************************************************************************** QUIZ / AJAX */

let xhr = new XMLHttpRequest();

initQuiz();

function initQuiz() {
  xhr.onreadystatechange = ladeFrage;

  xhr.open('POST', "service.php", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.send('startval=start');
}

function sendeAntwort() {
  let antwort = document.querySelector('input[name="antwort"]:checked').value;

  xhr.onreadystatechange = ladeFrage;

  xhr.open('POST', "service.php", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.send(`antwort=${antwort}`);
}


function sendeBenutzername() {
  let benutzername = document.getElementById("benutzername").value;

  xhr.onreadystatechange = ladeFrage;

  xhr.open('POST', "service.php", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.send(`benutzername=${benutzername}`);
}


function ladeFrage() {

  if(xhr.readyState == 4) {
    let respCode = xhr.responseText;

    document.getElementById('center-of-attention').innerHTML = respCode;
  }
}

var abort = false;

function checkBenutzername(val) {
  let benutzername = document.getElementById("benutzername").value;

  xhr.onreadystatechange = function() {
    if(xhr.readyState == 4) {
      let respCode = xhr.responseText;
    
      if(respCode.includes(document.getElementById("benutzername").value)) {
        document.getElementById("benutzername").value = "";
        document.getElementById("benutzername").style.border = "4px solid red";
        alert("Benutzername bereits vorhanden!");
        
      } else {
        abort = true;
      }
    }
  };

  xhr.open('POST', "service.php", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.send(`check_benutzer=${benutzername}`);

  return abort;
}

/***************************************************************************** CANVAS */
function draw_canvas() {
    var canvas = document.getElementById('richis-canvas');
    if (canvas.getContext) {
      var c = canvas.getContext('2d');

      const centerX = canvas.width / 2;

      // R
      c.moveTo(50, 50);

      c.lineTo(50, 150);
      c.moveTo(50, 50);
      c.arc(75, 75, 25, Math.PI * 1.5, 1.5);
      c.lineTo(50, 100);
      c.lineTo(100, 150);

      // I
      c.moveTo(125, 50);

      c.lineTo(125, 150);

      // C
      c.moveTo(200, 50);
      c.arc(200, 100, 50, Math.PI * 1.5, Math.PI / 2, true);

      // H
      c.moveTo(225, 50);
      c.lineTo(225, 150);

      c.moveTo(225, 100);
      c.lineTo(275, 100);

      c.moveTo(275, 50);
      c.lineTo(275, 150);

      // I
      c.moveTo(300, 50);

      c.lineTo(300, 150);

      // '
      c.moveTo(325, 50);

      c.lineTo(325, 75);

      // s
      startX = 350;
      endX = 400;
      startY = 50;
      endY = 150;

      midX = startX + ((endX - startX) / 2);
      midY = startY + ((endY - startY) / 2);

      c.moveTo(midX, startY);

      c.arc(midX, midY-25, 25, Math.PI * 1.5, Math.PI * 0.5, true);

      c.moveTo(midX, startY);
      c.lineTo(endX, startY);

      c.moveTo(midX, midY);

      c.arc(midX, midY+25, 25, Math.PI * 1.5, Math.PI * 0.5);

      c.lineTo(startX, endY);

      c.stroke();

      // Computerservice

      c.font = '50px Arial';
      c.fillStyle = '#cccccc';
      c.textAlign = 'center';
      c.fillText('Computerservice', centerX+10, 250);


    }
  }

  draw_canvas();