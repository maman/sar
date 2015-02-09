;(function () {
  'use strict';
  var data = {
    "2010": {
      "321010012": {
        "name": "Dimas Aditya Seputro",
        "app": "Sistem Informasi Akademik dan Portfolio Pembelajaran"
      }
    },
    "2011": {
      "321110001": {
        "name": "Achmad Mahardi Kusuma Rachman",
        "app": "Sistem Informasi E-Instrument Pembelajaran & Self Assesment Report Dosen"
      },
      "321110008": {
        "name": "Bobi Triyanto",
        "app": "Sistem Informasi Tracer Study dan Survey Kepuasan Pengguna Alumni"
      },
      "321110009": {
        "name": "Chintiya Zein Sakti",
        "app": "Sistem Informasi Kuisioner Kualitas Pelayanan Dengan Integrasi Metode Service Quality dan Importance Performance Analysis"
      },
      "321110015": {
        "name": "Kresnata Adi Surya",
        "app": "Sistem Informasi Approval Online Lembaga Kemahasiswaan"
      },
      "321110016": {
        "name": "Linda Martin Prasetyo",
        "app": "Sistem Informasi Ujian Online Dilengkapi dengan Pengembangan Sistem Informasi Eksekutif"
      },
      "321110018": {
        "name": "Monica",
        "app": "Sistem Informasi Ujian Online dengan Tes Psikologi Edward Personal Preference Schedule (EPPS) untuk Penerimaan Mahasiswa Baru"
      },
      "321110023": {
        "name": "Sekar Ayu Aulia Insani",
        "app": "Sistem Informasi E-Kuisioner Mutu Pengajaran"
      }
    },
    "2012": {
      "321210016": {
        "name": "Yogie Aryanto Prasetya",
        "app": "Sistem Informasi Dan Rekomendasi Penjadwalan Perkuliahan dan Peserta Perkuliahan"
      }
    }
  };
  var css = "embed-bg-cover {\
      position: absolute;\
      width: 100%;\
      height: 100%;\
      top: 0;\
      left: 0;\
      z-index: 999999999;\
      background: rgba(0, 0, 0, .4);\
    }";
  var sheet = (function() {
    var style = document.createElement('style');
    style.appendChild(document.createTextNode(''));
    document.head.appendChild(style);
    return style.sheet;
  })();
  function addModal() {
    var root = document.body;
    var modal = document.createElement('div');
    modal.setAttribute('class', 'embed-bg-cover');
    root.appendChild(modal);
    modal.addEventListener('click', function (evt) {
      root.removeChild(modal);
    });
  }
  document.addEventListener('DOMContentLoaded', function (evt) {
    var element = document.querySelector('[data-about-popup]');
    element.addEventListener('click', function (evt) {
      evt.preventDefault();
      sheet.insertRule(css, -1);
      addModal();
    }, false);
  });
})();
