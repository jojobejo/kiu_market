<!-- jQuery -->
<script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js') ?>"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo base_url('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<!-- DataTables -->
<script src="<?php echo base_url('assets/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/jszip/jszip.min.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/pdfmake/pdfmake.min.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/pdfmake/vfs_fonts.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-buttons/js/buttons.html5.min.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-buttons/js/buttons.print.min.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-buttons/js/buttons.colVis.min.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url('assets/dist/js/adminlte.min.js') ?>"></script>

<script>
    var table;
    $(document).ready(function() {
        bsCustomFileInput.init();

        var filterFokus  = '';
        var filterOnline = '';
        var defaultPreviewImage = '<?= base_url('images/Karisma.png') ?>';
        var saveBahanUrl = '<?= base_url('Sales/updateBahanAktif') ?>';
        var saveGambarUrl = '<?= base_url('Sales/updateGambarProduk') ?>';

        table = $('#table').DataTable({ 
            "processing": true,
            "serverSide": true,
            "order": [],
            "responsive": true,
            "ajax": {
                "url": "<?= base_url('Sales/getBarang') ?>",
                "type": "POST",
                "data": function(d) {
                    d.filter_fokus  = filterFokus;
                    d.filter_online = filterOnline;
                }
            },
            "columnDefs": [
                { "visible": false, "targets": [0] },
                { "orderable": false, "targets": [6,7,8] },
                { "className": "text-center", "targets": "_all" }
            ],
            "drawCallback": function() {
                $('.btn-edit-bahan').off('click').on('click', function() {
                    resetFeedback('#feedbackBahanAktif');
                    $('#edit_bahan_id_barang').val($(this).data('id'));
                    $('#edit_bahan_kode_barang').text($(this).data('kode'));
                    $('#edit_bahan_nama_barang').text($(this).data('nama'));
                    $('#edit_kelompok').val($(this).data('kelompok'));
                    $('#edit_bahan_aktif').val($(this).data('bahan'));
                    $('#modalEditBahanAktif').modal('show');
                });

                $('.btn-edit-gambar').off('click').on('click', function() {
                    var imageName = $(this).data('image');
                    var previewSrc = imageName && imageName !== '-'
                        ? '<?= base_url('images/produk/') ?>' + imageName
                        : defaultPreviewImage;

                    resetFeedback('#feedbackGambarProduk');
                    $('#formEditGambarProduk')[0].reset();
                    $('.custom-file-label[for="gambar_produk"]').text('Pilih file gambar');
                    $('#edit_gambar_id_barang').val($(this).data('id'));
                    $('#edit_gambar_kode_barang').text($(this).data('kode'));
                    $('#edit_gambar_nama_barang').text($(this).data('nama'));
                    $('#previewGambarProduk').attr('src', previewSrc);
                    $('#modalEditGambarProduk').modal('show');
                });

                $('.btn-online').off('click').on('click', function() {
                    $('#online_id_barang').val($(this).data('id'));
                    $('#online_kode_barang').val($(this).data('kode'));
                    $('#online_nama_barang').text($(this).data('nama'));
                    $('#switch_shopee').prop('checked', $(this).data('shopee') == 1);
                    $('#switch_tokopedia').prop('checked', $(this).data('tokopedia') == 1);
                    $('#switch_kiushop').prop('checked', $(this).data('kiushop') == 1);
                    $('#modalOnlineShop').modal('show');
                });
            }
        });

        // FILTER PRODUK FOKUS
        $('.btn-filter-fokus').click(function() {
            $('.btn-filter-fokus').removeClass('active');
            $(this).addClass('active');
            filterFokus = $(this).data('fokus');
            table.ajax.reload();
        });

        // Filter Online Shop ← tambahan baru
        $(document).on('click', '.btn-filter-online', function() {
            $('.btn-filter-online').each(function() {
                $(this).removeClass('active');
                // Reset style ke default
                var online = $(this).data('online');
                if (online == 'shopee') {
                    $(this).css({'background':'white', 'color':'#ee4d2d'});
                } else if (online == 'tokopedia') {
                    $(this).css({'background':'white', 'color':'#42b549'});
                } else if (online == 'kiushop') {
                    $(this).css({'background':'white', 'color':'#6096B4'});
                }
            });

            $(this).addClass('active');

            // Highlight tombol aktif
            var online = $(this).data('online');
            if (online == 'shopee') {
                $(this).css({'background':'#ee4d2d', 'color':'white'});
            } else if (online == 'tokopedia') {
                $(this).css({'background':'#42b549', 'color':'white'});
            } else if (online == 'kiushop') {
                $(this).css({'background':'#6096B4', 'color':'white'});
            }

            filterOnline = online;
            table.ajax.reload();
        });

        $('#gambar_produk').on('change', function() {
            var file = this.files && this.files[0] ? this.files[0] : null;
            if (!file) {
                $('#previewGambarProduk').attr('src', defaultPreviewImage);
                return;
            }

            var reader = new FileReader();
            reader.onload = function(e) {
                $('#previewGambarProduk').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        });

        $('#formEditBahanAktif').on('submit', function(e) {
            e.preventDefault();
            resetFeedback('#feedbackBahanAktif');

            var $button = $('#btnSimpanBahanAktif');
            var originalHtml = $button.html();
            $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Menyimpan...');

            $.ajax({
                url: saveBahanUrl,
                type: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status) {
                        showFeedback('#feedbackBahanAktif', response.message, true);
                        table.ajax.reload(null, false);
                        setTimeout(function() {
                            $('#modalEditBahanAktif').modal('hide');
                        }, 700);
                    } else {
                        showFeedback('#feedbackBahanAktif', response.message || 'Perubahan gagal disimpan.', false);
                    }
                },
                error: function() {
                    showFeedback('#feedbackBahanAktif', 'Terjadi kendala saat mengirim perubahan bahan aktif.', false);
                },
                complete: function() {
                    $button.prop('disabled', false).html(originalHtml);
                }
            });
        });

        $('#formEditGambarProduk').on('submit', function(e) {
            e.preventDefault();
            resetFeedback('#feedbackGambarProduk');

            var formData = new FormData(this);
            var $button = $('#btnSimpanGambarProduk');
            var originalHtml = $button.html();
            $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Mengunggah...');

            $.ajax({
                url: saveGambarUrl,
                type: 'POST',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status) {
                        showFeedback('#feedbackGambarProduk', response.message, true);
                        if (response.image_url) {
                            $('#previewGambarProduk').attr('src', response.image_url);
                        }
                        table.ajax.reload(null, false);
                        setTimeout(function() {
                            $('#modalEditGambarProduk').modal('hide');
                        }, 700);
                    } else {
                        showFeedback('#feedbackGambarProduk', response.message || 'Gambar gagal diperbarui.', false);
                    }
                },
                error: function() {
                    showFeedback('#feedbackGambarProduk', 'Upload gambar gagal diproses. Silakan coba lagi.', false);
                },
                complete: function() {
                    $button.prop('disabled', false).html(originalHtml);
                }
            });
        });

        $('#modalEditBahanAktif, #modalEditGambarProduk').on('hidden.bs.modal', function() {
            resetFeedback('#feedbackBahanAktif');
            resetFeedback('#feedbackGambarProduk');
        });

        function showFeedback(selector, message, isSuccess) {
            $(selector)
                .removeClass('is-success is-error')
                .addClass(isSuccess ? 'is-success' : 'is-error')
                .show()
                .text(message);
        }

        function resetFeedback(selector) {
            $(selector)
                .removeClass('is-success is-error')
                .hide()
                .text('');
        }

    });

    // Set the date we're counting down to
    var countDownDate = new Date("Mar 1, 2023 00:00:00").getTime();

    // Update the count down every 1 second
    var x = setInterval(function() {

        // Get today's date and time
        var now = new Date().getTime();

        // Find the distance between now and the count down date
        var distance = countDownDate - now;

        // Time calculations for days, hours, minutes and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Display the result in the element with id="demo"
        document.getElementById("demo").innerHTML = days + " " + "Hari" + " " + "-" + " " + hours + " " + "Jam" + " " +
            "-" + " " + minutes + " " + "Menit" + " " + "-" + " " + seconds + "s";

        // If the count down is finished, write some text
        if (distance < 0) {
            clearInterval(x);
            document.getElementById("demo").innerHTML = "EXPIRED";
        }
    }, 1000);
</script>

</body>

</html>
