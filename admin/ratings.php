<?php
include '../connection/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

// Fetch ratings data
$ratings_query = "SELECT * FROM ratings";
$ratings_stmt = $conn->query($ratings_query);
$ratings = $ratings_stmt->fetchAll(PDO::FETCH_ASSOC);

// Post rating
if (isset($_POST['post-rating'])) {
    $ratings_id = $_POST['ratings_id_to_post'];
    $post_query = "UPDATE ratings SET status = 1 WHERE id = ?";
    $stmt_post = $conn->prepare($post_query);
    $stmt_post->execute([$ratings_id]);

    if ($stmt_post->rowCount() > 0) {
        $_SESSION['success'] = 'Rating posted successfully!';
        header('location: ratings.php');
        exit;
    } else {
        $_SESSION['error'] = 'Error posting rating.';
    }
}


// delete ratings
if (isset($_POST['delete-ratings'])) {
    $ratings_id = $_POST['ratings_id_to_delete'];
    $delete_query = "DELETE FROM ratings WHERE id = ?";
    $stmt_delete = $conn->prepare($delete_query);
    $stmt_delete->execute([$ratings_id]);

    if ($stmt_delete->rowCount() > 0) {
        $_SESSION['success'] = 'Ratings deleted successfully!';
        header('location: ratings.php');
        exit;
    } else {
        $_SESSION['error'] = 'Error deleting ratings.';
    }
}


?>

<?php include 'header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Ratings</h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Message</th>
                            <td>Status</td>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ratings as $rating): ?>
                            <tr>
                                <td><?php echo $rating['id'] ?></td>
                                <td><?php echo $rating['name'] ?></td>
                                <td><?php echo $rating['email'] ?></td>
                                <td><?php echo $rating['message'] ?></td>
                                <td>
                                    <?php echo ($rating['status'] == 1) ? 'Already Posted' : 'Not Posted'; ?>
                                </td>

                                <td class="text-center">
                                    <a href="#" class="btn btn-primary post" data-toggle="modal" data-target="#postRating" data-id="<?php echo $rating['id']; ?>">
                                        <i class="fas fa-edit"></i> Post
                                    </a>
                                    <a href="#" class="btn btn-danger delete" data-toggle="modal" data-target="#deleteRatings"
                                        data-id="<?php echo $rating['id'] ?>">
                                        <i class="fas fa-trash"> Delete</i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="postRating" tabindex="-1" role="dialog" aria-labelledby="postRatingLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="postRatingLabel">Post Rating</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to post this rating?</p>
                <form method="POST">
                    <input type="hidden" name="ratings_id_to_post" id="ratings_id_to_post">
                    <button class="btn btn-primary" type="submit" name="post-rating">Yes, Post</button>
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="deleteRatings" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete Rating</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this rating?</p>
                <form method="POST">
                    <input type="hidden" name="ratings_id_to_delete" id="ratings_id_to_delete">
                    <button class="btn btn-danger" type="submit" name="delete-ratings">Yes, Delete</button>
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="assets/admin/vendor/jquery/jquery.min.js"></script>

<?php include 'footer.php' ?>

<script>
    $('#postRating').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var ratingId = button.data('id');

        var modal = $(this);
        modal.find('#ratings_id_to_post').val(ratingId);
    });


    $('#deleteRatings').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var ratingId = button.data('id');

        var modal = $(this);
        modal.find('#ratings_id_to_delete').val(ratingId);
    });
</script>