@extends('app.master')

@section('content')

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h1 class="modal-title fs-5" id="reportModalLabel">Report</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reportForm">
                <input type="hidden" name="userId" id="reportUserId">
                <input type="hidden" name="blogId" id="reportBlogId">
                <input type="hidden" name="commentId" id="reportCommentId">

                <div class="modal-body bg-dark">
                    <div class="form-group">
                        <label for="reason" class="form-label">Reason: </label>
                        <input type="text" class="form-control" name="reason" id="reason" required>
                    </div>
                </div>
                <div class="modal-footer bg-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editBlogModal" tabindex="-1" aria-labelledby="editBlogModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h1 class="modal-title fs-5" id="editBlogModalLabel">Edit Blog</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBlog" enctype="multipart/form-data">
                <div class="modal-body bg-dark">
                    <input type="hidden" class="form-control" name="id" id="edit_blog_id" required>
                    <div class="form-group">
                        <label for="blog_title" class="form-label">Blog Title: </label>
                        <input type="text" class="form-control" name="title" id="edit_blog_title" required>
                    </div>
                    <div class="form-group">
                        <label for="blog_content" class="form-label">Blog Content: </label>
                        <!-- Textarea for TinyMCE editor -->
                        <textarea class="form-control" name="content" id="edit_blog_content" cols="30" rows="10"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-dark">
                    <button type="submit" class="btn btn-secondary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card bg-dark mb-3">
    <div class="card-header bg-secondary text-white fs-4 d-flex justify-content-between">
        {{ $blog->title }}

        <div class="btn-group">
            <button type="button" class="btn btn-secondary" data-bs-toggle="dropdown" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                    <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                </svg>
            </button>
            <ul class="dropdown-menu">
                @if(Auth::check() && Auth::id() == $blog->user_id)
                    <li><a class="dropdown-item" href="#" id="deleteBlog" data-id="{{ encrypt($blog->id) }}">Delete Blog</a></li>
                    <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editBlogModal"
                        data-id="{{encrypt($blog->id)}}"
                        data-title = "{{$blog->title}}"
                        data-content = "{{$blog->content}}"
                        >
                        Edit</a>
                    </li>
                @else
                <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reportModal" data-name="blog" data-blogid="{{ encrypt($blog->id) }}">Report Blog</a></li>
                <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reportModal" data-name="user" data-userid="{{ encrypt($blog->user->id) }}">Report User</a></li>
                @endif
            </ul>
        </div>
    </div>
    <div class="card-body text-white text-center">
        {!! $blog->content !!}
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <a href="{{route('profile.userprofile', encrypt($blog->user->id))}}" class="text-decoration-none">
            <div class="user d-flex align-items-center">
                @if($blog->user->profile && $blog->user->profile->profile_image)
                    <img src="{{ asset('storage/' . $blog->user->profile->profile_image) }}" alt="Profile Image" class="rounded-circle" width="40" height="40">
                @else
                    <img src="{{ asset('defaults/default_profile.jpeg') }}"
                        alt="No profile photo"
                        title="No profile photo"
                        class="rounded-circle" width="40" height="40">
                @endif
                <span class="ms-2 text-white">{{ $blog->user->name }}</span>
            </div>
        </a>
        <div class="like">
            <button type="button" class="btn btn-primary" id="likeButton" value="{{ encrypt($blog->id) }}">
                @if(auth()->check() && auth()->user()->likedBlogs()->where('blog_id', $blog->id)->exists())
                    Unlike
                @else
                    Like
                @endif
            </button>
            <span id="likeCount{{ $blog->id }}" class="text-white">
                {{ $blog->likedUsers()->count() }} Likes
            </span>
        </div>
    </div>
</div>

<!-- Comment Form -->
<div class="card bg-dark">
    <form action="{{ route('conversation.storecomment') }}" method="POST">
        <div class="card-body">
            @csrf
            <input type="hidden" name="blogId" value="{{encrypt($blog->id)}}">
            <div class="form-group">
                <label for="comment" class="text-white">Comment</label>
                <textarea name="comment" id="comment" class="form-control" required></textarea>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Comment</button>
        </div>
    </form>
</div>

<!-- Display Comments -->
<div class="card bg-dark mt-3">
    <div class="card-header bg-secondary text-white fs-4">
        Comments
    </div>
    <div class="card-body bg-dark p-4 rounded-bottom">
        @foreach($blog->comments as $comment)
            <div class="comment mb-4 d-flex justify-content-start align-items-start">
                <div class="d-flex align-items-center">
                    <!-- User Profile Image and Name -->
                    @if($comment->user) <!-- If the comment is from a logged-in user -->
                        <a href="{{route('profile.userprofile', encrypt($comment->user->id))}}" class="text-decoration-none">
                            @if($comment->user->profile && $comment->user->profile->profile_image)
                                <img src="{{ asset('storage/' . $comment->user->profile->profile_image) }}" alt="Profile Image" class="rounded-circle border border-light" width="40" height="40">
                            @else
                                <img src="{{ asset('defaults/default_profile.jpeg') }}" alt="No profile photo" class="rounded-circle border border-light" width="40" height="40">
                            @endif
                            <span class="ms-3 text-white fw-bold">{{ $comment->user->name }}</span>
                            <p class="text-secondary mt-2 small">{{ $comment->created_at->diffForHumans() }}</p>
                        </a>
                    @else <!-- If the comment is from a guest -->
                        <span class="ms-3 text-white fw-bold">No User</span>
                    @endif
                </div>

                <div class="ms-4 w-100 overflow-auto">
                    <!-- Comment Text -->
                    <p class="text-white mt-2 fs-5" style="white-space: pre-wrap; word-wrap: break-word; overflow-wrap: break-word;">
                        {!! nl2br(e($comment->comment)) !!}
                    </p>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-dark" data-bs-toggle="dropdown" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                            <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                        </svg>
                    </button>
                    <ul class="dropdown-menu">
                        @if(Auth::check() && (Auth::id() == $blog->user_id || Auth::id() == $comment->user_id))
                            <li><a class="dropdown-item" href="#" id="deleteComment" data-id="{{ encrypt($comment->id) }}">Delete Comment</a></li>
                        @else
                            <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reportModal" data-name="comment" data-commentid="{{ encrypt($comment->id) }}">Report Comment</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
</div>

@endsection

@section('scripts')
<script>
    tinymce.init({
        selector: '#edit_blog_content',
        menubar: false,  // Optional: disable the menu bar
        plugins: 'link image lists',  // Optional: you can add more plugins if needed
        toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | link image', // Optional: customize the toolbar
    });
</script>

<script>

    $('#editBlogModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var blogId = button.data('id'); // Extract info from data-* attributes
        var blogTitle = button.data('title');
        var blogContent = button.data('content');

        // Populate the modal's fields with the data
        var modal = $(this);
        modal.find('#edit_blog_id').val(blogId);
        modal.find('#edit_blog_title').val(blogTitle);
        modal.find('#edit_blog_content').val(blogContent);

        tinymce.get('edit_blog_content').setContent(blogContent);  // Initialize TinyMCE editor with blog content
    });

    $(document).on('submit', '#editBlog',function (e) {
        e.preventDefault();

        console.log('Inside');

        var formData = new FormData(this);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "{{route('blog.updateblog')}}",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log(response);
                if (response.status == 200) {
                    // Flash success message using SweetAlert2
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    });

                    setTimeout(function() {
                        $('#createBlogModal').modal('hide');
                        location.reload();
                    }, 2000);
                } else if (response.status == 404) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Not Found',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(function() {
                                location.reload();
                            });
                } else {
                    // Flash error message using SweetAlert2
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    });
                }

            },
            error: function(xhr, status, error) {
                // Flash error if request fails and show the specific error message

                // If validation errors are returned from Laravel
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    let errorMessage = 'Validation errors occurred:';

                    // Loop through the validation errors and show them in a single message
                    $.each(xhr.responseJSON.errors, function(field, messages) {
                        errorMessage += `\n${messages.join(', ')}`;
                    });

                    // Show the validation errors in SweetAlert2
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Errors!',
                        text: errorMessage,
                        confirmButtonText: 'OK'
                    });
                } else {
                    // If some other error occurs (e.g., server error)
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong. Please try again.',
                        confirmButtonText: 'OK'
                    });
                }
            }
        });

    });

    $(document).on('click', '#deleteBlog', function (e) {
        e.preventDefault();

        var blogId = $(this).data('id');

        var formData = new FormData();
        formData.append('id', blogId);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to delete this blog?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('blog.deleteblog') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status == 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(function() {
                                window.location.href = response.url;
                            });
                        } else if (response.status == 404) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Not Found',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(function() {
                                location.reload();
                            });
                        }
                    },

                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    });

    $(document).on('click', '#deleteComment', function (e) {
        e.preventDefault();

        var blogId = $(this).data('id');

        var formData = new FormData();
        formData.append('id', blogId);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to delete this comment?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('conversation.deletecomment') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status == 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(function() {
                                location.reload();
                            });
                        } else if (response.status == 404) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Not Found',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(function() {
                                location.reload();
                            });
                        }
                    },

                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    });

    $(document).on('click', '#likeButton', function (e) {
        e.preventDefault();  // Prevent the default action

        var blogId = $(this).val();  // Get the blog ID from the button value
        var button = $(this);  // The like/unlike button
        var likeCountElement;  // The like count span for this blog

        $.ajax({
            url: "{{ route('blog.likeblog') }}",  // Your route for liking the blog
            method: 'POST',
            data: {
                blog_id: blogId,
                _token: '{{ csrf_token() }}',  // CSRF token for security
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Toggle the button text based on the response
                    if (response.message === 'added') {
                        button.text('Unlike');  // Change button text to 'Unlike'
                    } else {
                        button.text('Like');  // Change button text back to 'Like'
                    }

                    // Update the like count on the UI
                    likeCountElement = $('#likeCount' + response.blogId);
                    likeCountElement.text(response.likeCount + ' Likes');  // Set the new like count
                } else {
                    alert('Something went wrong. Please try again.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('There was an error. Please try again later.');
            }
        });
    });

    $('#reportModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        // Retrieve data attributes from the clicked button
        var buttonName = button.data('name');
        var blogId = button.data('blogid');
        var userId = button.data('userid');
        var commentId = button.data('commentid');

        console.log(buttonName);

        // Pass the data to the modal form fields
        var modal = $(this);

        if(buttonName === 'blog'){
            modal.find('#reportModalLabel').html('Report Blog');
        }

        if(buttonName === 'user'){
            modal.find('#reportModalLabel').html('Report User');
        }

        if(buttonName === 'comment'){
            modal.find('#reportModalLabel').html('Report Comment');
        }
        // Reset form fields before populating
        $('#reportUserId').val('');
        $('#reportBlogId').val('');
        $('#reportCommentId').val('');
        $('#reason').val('');

        if (userId) {
            modal.find('#reportUserId').val(userId);
        }

        if (blogId) {
            modal.find('#reportBlogId').val(blogId);
        }

        if (commentId) {
            modal.find('#reportCommentId').val(commentId);
        }
    });

    $(document).on('submit', '#reportForm', function (e) {
        e.preventDefault();  // Prevent default form submission

        // Collect the form data
        var formData = {
            userId: $('#reportUserId').val(),
            blogId: $('#reportBlogId').val(),
            commentId: $('#reportCommentId').val(),
            reason: $('#reason').val(),
            reportableType: $('#reportableType').val(),
            _token: '{{ csrf_token() }}'  // CSRF token for security
        };

        // Log the collected data for debugging (optional)
        console.log(formData);

        // Send the data via AJAX
        $.ajax({
            url: "{{ route('report') }}",  // Make sure the route is correct
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.status === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Reported',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(function() {
                        location.reload(); // Reload page or update the UI
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        confirmButtonText: 'OK'
                    });
                }
            },

            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong. Please try again.',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

</script>
@endsection
