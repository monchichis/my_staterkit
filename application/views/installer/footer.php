            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS (jQuery already loaded in header) -->
    <script src="<?php echo base_url('assets/template/js/bootstrap.min.js'); ?>"></script>
    
    <?php if ($this->session->flashdata('success')): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?php echo $this->session->flashdata('success'); ?>',
            confirmButtonText: 'OK'
        });
    </script>
    <?php endif; ?>
    
    <?php if ($this->session->flashdata('error')): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '<?php echo $this->session->flashdata('error'); ?>',
            confirmButtonText: 'OK'
        });
    </script>
    <?php endif; ?>
</body>
</html>
