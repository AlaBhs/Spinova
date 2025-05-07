<?php include __DIR__ . '/../partials/header.php'; ?>

<main class="content-wrapper">
    <div class="container content" style="max-width: 90% !important; padding: 0 !important;">
        <div class="row">
            <div class="col-lg-12">
            
                <?php if (empty($links)) : ?>
               
                    <script type="text/javascript">
                        const wrapper = document.querySelector('main.content-wrapper');
                        wrapper.classList.add('vertically-center');
                    </script>

                    <div class="no-data-container rounded shadow-sm">
                        <div class="no-data-container_btn text-center">
                            <p>No item to show!</p>
                        </div>
                    </div>

                <?php else : ?>

                    <div class="content-container table-responsive shadow-sm rounded">
                        <table id="dash-link-table" class="table mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">LINK</th>
                                    <th scope="col">TOTAL VISITS</th>
                                    <th scope="col">REDIRECTS TO</th>
                                    <th scope="col">VISITS</th>
                                    <th scope="col">PERCENTAGE</th>
                                    <th scope="col">CLICKS</th>
                                    <th scope="col">OS</th>
                                </tr>
                            </thead>

                            <?php echo $links; ?>

                        </table>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </div>
  
    <!-- Delete Modal -->
    <!-- Delete Modal -->
    <div class="modal" id="exampleModalCenter" data-easein="slideDownBigIn" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="exampleModalLongTitle">DELETE CONFIRMATION</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                        
                </div>

                <div class="modal-body">

                    <p>Are you sure you want to delete?</p>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-success btn-sm" data-dismiss="modal">No</button>               

                    <form method="POST" class="d-inline-block delete-form">
                        
                        <button type="submit" class="btn btn-danger btn-sm">Yes, Delete</button>
                    
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>