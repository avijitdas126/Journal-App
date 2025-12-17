<?php
require_once __DIR__ . '/../../utils/db_conn.php';
$conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));

$article_id = $_GET['id'];
$student_id = $_SESSION['user_id'];

// --------------------
// GET ARTICLE
// --------------------
$sql = "SELECT * FROM article WHERE article_id = :id AND author_id = :student";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $article_id, ':student' => $student_id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article)
    die("<h2>You cannot access this article.</h2>");

// --------------------
// GET CATEGORY
// --------------------
$sql = "SELECT * FROM category WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $article['category']]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

// --------------------
// GET ALL REVIEWS
// --------------------
$sql = "
    SELECT r.*, a.name AS reviewer_name
    FROM reviews r
    LEFT JOIN admins a ON a.admin_id = r.reviewer_id
    WHERE r.article_id = :id
    ORDER BY r.created_at ASC
";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $article_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="container-fluid py-3" style="height:100vh; overflow-y:scroll;">

    <h2>Resubmit Article</h2>

    <!-- Article Info -->
    <div class="card mb-3">
        <div class="card-body">
            <h4><?php echo htmlspecialchars($article['title']); ?></h4>
            <?php if ($category) { ?>
                <span class="badge bg-info text-dark">Category: <?php echo $category['category']; ?></span>
            <?php } ?>
            <span class="badge bg-secondary">Author ID: <?php echo $article['author_id']; ?></span>
            <span class="badge bg-warning text-dark">Status: <?php echo $article['status']; ?></span>
        </div>
    </div>

    <!-- Teacher Feedback -->
    <h4>Teacher Feedback</h4>
    <div class="card mb-3" style="max-height:300px; overflow-y:auto;">
        <div class="card-body bg-light">

            <?php if (!$reviews)
                echo "<p class='text-muted'>No review messages yet.</p>"; ?>

            <?php foreach ($reviews as $rev) { ?>
                <div class="border p-2 rounded mb-2">
                    <small class="text-muted">
                        Teacher: <?php echo htmlspecialchars($rev['reviewer_name']); ?>
                        • <?php echo $rev['created_at']; ?>
                    </small>
                    <p class="mt-2"><?php echo nl2br(htmlspecialchars($rev['reviewer_text'])); ?></p>
                </div>
            <?php } ?>
        </div>
    </div>
<div class="container" style="margin-bottom: 30px;padding:20px;">
    <!-- Editor Section -->
    <h4>Revise Your Article</h4>
    
    <form action="http://localhost/Journal/views/components/api/student_resubmit.php" method="POST"
        >

        <input type="hidden" name="article_id" value="<?php echo $article_id; ?>">
        <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Title of Article: <span
                    class="badge text-bg-secondary" id="badged" ></span></label>
            <input type="text" class="form-control" name="article_title" id="titleinput" value="<?php echo $article['title']?>" placeholder="Enter the title of your article">
        </div>
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Category: </label>
          <select class="form-select" aria-label="role" name="catagory" id="category_id" required>
    <option value="">Select a category</option>
    <?php
    $stmt = $conn->prepare("SELECT * FROM `category`;");
    $stmt->execute();
    $categorys = $stmt->fetchAll();

    // Define the current category ID you want to match
    $current_category_id = $article['category'];

    foreach ($categorys as $category) {
        // Check if the current category ID matches the iteration ID
        $isSelected = ($current_category_id == $category['id']) ? 'selected' : '';
        ?>
        
        <!-- Add the $isSelected variable within the option tag -->
        <option value="<?php echo $category['id']; ?>" <?php echo $isSelected; ?>>
            <?php echo $category['category']; ?>
        </option>
        
        <?php
    }
    ?>
</select>

        </div>
        <div class="card p-3 mb-3">
            <label class="form-label">Updated Article</label>
            <div class="editor-scroll-box bg-white p-3 rounded shadow-sm">
                <div id="editorjs"></div>
            </div>
            <input type="hidden" name="content_json" id="content_json">
            <input type="hidden" name="content_html" id="content_html">
        </div>
<div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="4"
                placeholder="Enter a brief description of your article"><?php echo htmlspecialchars($article['description']) ?></textarea>
        </div>


        <!-- Student note -->
        <div class="mb-3">
            <label class="form-label">Message to Teacher</label>
            <textarea class="form-control" name="student_message" id="student_message" rows="4"
                placeholder="Explain what changes you made"></textarea>
        </div>

        <button class="btn btn-primary" type="submit" onclick="saveFinalData()">Submit Resubmission</button>
    </form>
</div>
</div>

<style>
    /* Scrollable wrapper */
    .editor-scroll-box {
        width: 100%;
        max-height: 500px;
        /* Adjust height as you want */
        overflow-y: auto;
        /* Enable vertical scroll */
        overflow-x: hidden;
        /* Prevent horizontal scroll */
    }

    /* Ensure all editor content fits inside */
    #editorjs,
    #editorjs .ce-block,
    #editorjs .ce-block__content {
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 !important;
    }

    /* Block padding fix */
    #editorjs .ce-block__content {
        padding: 10px 0 !important;
    }

    /* Prevent paragraph overflow */
    #editorjs .ce-paragraph {
        width: 100% !important;
        word-break: break-word !important;
    }

    /* Fix images */
    #editorjs .image-tool__image-picture {
        max-width: 100% !important;
        height: auto !important;
    }

    /* Fix toolbar */
    #editorjs .ce-toolbar__actions {
        right: 5px !important;
    }

    /* FORCE EDITOR.JS TO BE FULL WIDTH */
    #editorjs,
    #editorjs .ce-block,
    #editorjs .ce-block__content,
    #editorjs .ce-toolbar__content {
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    /* Prevent sideways scrolling */
    .editor-scroll-box {
        overflow-x: hidden !important;
    }

    /* Fix toolbar placement */
    #editorjs .ce-toolbar__actions {
        right: 10px !important;
    }

    .ce-block__content {
        max-width: 700px;
        margin: 0 auto;
    }

    @media (max-width: 576px) {

        #editorjs,
        #editorjs .ce-block,
        #editorjs .ce-block__content {
            max-width: 100% !important;
            width: 100% !important;
        }
    }
</style>



<script src="<?php baseurl("js/bootstrap.min.js") ?>"></script>
<script src="<?php baseurl("js/lib/editorjs@latest.js") ?>"></script>
<script src="<?php baseurl("js/lib/header@latest.js") ?>"></script>
<script src="<?php baseurl("js/lib/list@2.js") ?>"></script>
<script src="<?php baseurl("js/lib/quote.umd.min.js") ?>"></script>
<script src="<?php baseurl("js/lib/image@latest.js") ?>"></script>
<script src="<?php baseurl("js/lib/marker@latest.js") ?>"></script>
<script src="<?php baseurl("js/lib/underline.umd.min.js") ?>"></script>
<script src="<?php baseurl("js/lib/raw.js") ?>"></script>
<script src="<?php baseurl("js/lib/checklist@latest.js") ?>"></script>
<script src="<?php baseurl("js/lib/link@latest.js") ?>"></script>
<script>
    window.addEventListener("beforeunload", () => {
        localStorage.removeItem('article_title');
        localStorage.removeItem('article');
        localStorage.removeItem('article_id');
    });
    window.addEventListener("pagehide", () => {
        localStorage.removeItem("article_title");
        localStorage.removeItem("article");
        localStorage.removeItem("article_id");
    });
</script>
<script>


    function convertDataToHtml(editorData) {
        let html = "";

        editorData.blocks.forEach(block => {
            switch (block.type) {

                // Header
                case "header":
                    html += `<h${block.data.level}>${block.data.text}</h${block.data.level}>`;
                    break;

                // Paragraph
                case "paragraph":
                    html += `<p>${block.data.text}</p>`;
                    break;

                // List
                case "List":
                    let listTag = "ul";
                    if (block.data.style == "ordered") {
                        listTag = "ol";
                        html += `<${listTag}>`;
                        block.data.items.forEach(item => {
                            html += `<li>${item.content}</li>`;
                        });
                        html += `</${listTag}>`;
                    } else if (block.data.style == "unordered") {
                        listTag = "ul";
                        html += `<${listTag}>`;
                        block.data.items.forEach(item => {
                            html += `<li>${item.content}</li>`;
                        });
                        html += `</${listTag}>`;
                    }
                    else if (block.data.style == "checklist") {
                        html += `<ul class="checklist">`;
                        block.data.items.forEach(item => {
                            html += `
                        <li>
                            <input type="checkbox" ${item.meta.checked ? "checked" : ""}>
                            ${item.content}
                        </li>`;
                        });
                        html += `</ul>`;
                    }

                    break;

                // Quote
                case "quote":
                    html += `
                    <blockquote>
                        <p>${block.data.text}</p>
                        ${block.data.caption ? `<cite>${block.data.caption}</cite>` : ""}
                    </blockquote>
                `;
                    break;

                // Image
                case "image":
                    html += `
                    <figure>
                        <img class="img-fluid" src="${block.data.file.url}" alt="${block.data.caption}">
                        ${block.data.caption ? `<figcaption>${block.data.caption}</figcaption>` : ""}
                    </figure>
                `;
                    break;

                // Marker (highlight text)
                case "marker":
                    html += `<mark>${block.data.text}</mark>`;
                    break;

                // Underline
                case "underline":
                    html += `<u>${block.data.text}</u>`;
                    break;
                // Link Tool
                case "linkTool":
                    html += `<a href="${block.data.link}" target="_blank">${block.data.link}</a>`;
                    break;

                // Raw HTML
                case "raw":
                    html += block.data.html;
                    break;

                // Delimiter
                case "delimiter":
                    html += "<hr>";
                    break;

                // Embed (YouTube, Vimeo, etc.)
                case "embed":
                    html += `
                    <iframe
                        width="560"
                        height="315"
                        src="${block.data.embed}"
                        frameborder="0"
                        allowfullscreen>
                    </iframe>`;
                    break;

                default:
                    console.warn("Unknown block", block.type);
            }
        });

        return html;
    }


</script>
<script>
    let editor = new EditorJS({
        /**
         * Id of Element that should contain Editor instance
         */
        holder: "editorjs",
        onChange: (api, event) => {
            debouncedSave(api)
        },
        tools: {
            header: Header,
            quote: {
                class: Quote,
                inlineToolbar: true,
                shortcut: 'CMD+SHIFT+O',
                config: {
                    quotePlaceholder: 'Enter a quote',
                    captionPlaceholder: 'Quote\'s author',
                },
            },
            underline: Underline,
            Marker: {
                class: Marker,
                shortcut: 'CMD+SHIFT+M',
            },
            List: {
                class: EditorjsList,
                inlineToolbar: true,
                config: {
                    defaultStyle: "unordered",
                },
            },
            raw: RawTool,
            image: {
                class: ImageTool,
                config: {
                    endpoints: {
                        byFile: 'http://localhost/Journal/views/components/api/asset.php', // Your backend file uploader endpoint
                        byUrl: 'http://localhost/Journal/views/components/api/asset.php', // Your endpoint that provides uploading by Url
                    }
                }
            },
            checklist: {
                class: Checklist,
            }
        },
        data: <?php echo $article['content_json']; ?>,
    });


    // Convert Editor.js blocks → HTML
    function convertBlocksToHTML(data) {
        let html = "";
        data.blocks.forEach(block => {
            switch (block.type) {
                case "header":
                    html += `<h${block.data.level}>${block.data.text}</h${block.data.level}>`;
                    break;
                case "paragraph":
                    html += `<p>${block.data.text}</p>`;
                    break;
                case "image":
                    html += `<img src="${block.data.file.url}" class="img-fluid">`;
                    break;
                default:
                    html += `<p>${block.data.text || ''}</p>`;
            }
        });
        return html;
    }

    // Save JSON + HTML before submit
    function saveFinalData() {
        editor.save().then(data => {
            document.querySelector("#content_json").value = JSON.stringify(data);
            document.querySelector("#content_html").value = convertDataToHtml(data);
            
            document.querySelector("form").submit();
        });
    }
</script>