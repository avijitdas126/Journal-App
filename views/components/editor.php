<?php
$id = $_GET['id'];
$page = $_GET['page'];
require_once __DIR__ . '/../../utils/db_conn.php';
$conn = db_conn(
  Env('servername'),
  Env('db'),
  Env('username'),
  Env('password')
);
$mode = $_GET['mode'] ?? 'draft';
?>
<div class="container py-3 mb-5">
  <div class="row">
    <div class="col-md-12">
      <div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">Title of Article: <span class="badge text-bg-secondary"
            id="badged"></span></label>
        <input type="text" class="form-control" id="titleinput" placeholder="Enter the title of your article">
      </div>
      <div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">Category: </label>
        <select class="form-select" aria-label="role" name="department_id" id="category_id" required>
          <?php
          $stmt = $conn->prepare("SELECT * FROM `category`;");
          $stmt->execute();
          $categorys = $stmt->fetchAll();
          foreach ($categorys as $category) {
            ?>
            <option value="<?php echo $category['id'] ?>"><?php echo $category['category'] ?>
            </option> <?php
          }
          ?>
        </select>
      </div>
      <div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">Description of Article: <span
            class="badge text-bg-secondary" id="badged"></span></label>
        <input type="text" class="form-control" id="desinput" placeholder="Enter the description of your article">
      </div>
      <div class="editor-scroll-box">
        <div id="editorjs"></div>
      </div>

    </div>
  </div>
</div>

<style>
  /* Scrollable wrapper */
  /* Editor container */
  .editor-scroll-box {
    height: 600px;
    /* fixed height */
    overflow-y: auto;
    /* enable scrolling */
    overflow-x: hidden;
    background: #fff;
    border-radius: 8px;
    padding: 16px;
    box-shadow: 0 2px 12px 4px rgba(0, 0, 0, 0.05);
  }

  /* Let Editor.js control layout */
  #editorjs {
    width: 100%;
  }

  /* Blocks should NOT be constrained */
  .ce-block,
  .ce-block__content {
    max-width: 100% !important;
    width: 100%;
    margin: 0;
  }

  /* Toolbar position fix */
  .ce-toolbar__actions {
    right: 10px;
  }

  /* Images responsive */
  .image-tool__image-picture img {
    max-width: 100%;
    height: auto;
  }

  /* Mobile */
  @media (max-width: 576px) {
    .editor-scroll-box {
      height: 70vh;
    }
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
    localStorage.removeItem('article_description');
  });
  window.addEventListener("pagehide", () => {
    localStorage.removeItem("article_title");
    localStorage.removeItem("article");
    localStorage.removeItem("article_id");
    localStorage.removeItem('article_description');
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
  const draft = document.querySelector('#badged')
  const titleinput = document.querySelector('#titleinput')
  const desinput = document.querySelector('#desinput')
  const category_id = document.querySelector('#category_id')
  category_id.addEventListener('input', (e) => {
    window.localStorage.setItem('category_id', e.target.value)
    let timerDraft = setTimeout(() => {
      draft.innerHTML = "Saving..."
    }, 2000)
    setTimeout(() => {
      draft.innerHTML = "";
    }, 6000);
    let payload = {
      article_id: <?php echo $id; ?>,
      isfull: false,
      category_id: window.localStorage.getItem('category_id') || 0,
      description: window.localStorage.getItem("article_description") || "No description",
      slug: window.localStorage.getItem("article_title") ? "<?php echo $_SESSION['username']; ?>_" + window.localStorage.getItem("article_title").toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]+/g, '') + '_' + <?php echo $id; ?> : "<?php echo $_SESSION['username']; ?>_" + "untitled-article_" + <?php echo $id; ?>,
      title: window.localStorage.getItem("article_title") || "Untitled Article",
      description: window.localStorage.getItem("article_description") || "No description",
    }
    run(payload).then(data => {
      console.log(data)
    }).catch(e => {
      console.log(e)
    })
  })
  desinput.addEventListener('input', (e) => {
    window.localStorage.setItem("article_description", e.target.value)
    let timerDraft = setTimeout(() => {
      draft.innerHTML = "Saving..."
    }, 2000)
    setTimeout(() => {
      draft.innerHTML = "";
    }, 6000);
    let payload = {
      article_id: <?php echo $id; ?>,
      isfull: false,
      category_id: window.localStorage.getItem('category_id') || 0,
      slug: window.localStorage.getItem("article_title") ? "<?php echo $_SESSION['username']; ?>_" + window.localStorage.getItem("article_title").toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]+/g, '') + '_' + <?php echo $id; ?> : "<?php echo $_SESSION['username']; ?>_" + "untitled-article_" + <?php echo $id; ?>,
      title: window.localStorage.getItem("article_title") || "Untitled Article",
      description: window.localStorage.getItem("article_description") || "No description",
    }
    run(payload).then(data => {
      console.log(data)
    }).catch(e => {
      console.log(e)
    })
  })
  titleinput.addEventListener('input', (e) => {
    window.localStorage.setItem("article_title", e.target.value)
    let timerDraft = setTimeout(() => {
      draft.innerHTML = "Saving..."
    }, 2000)
    setTimeout(() => {
      draft.innerHTML = "";
    }, 6000);
    let payload = {
      article_id: <?php echo $id; ?>,
      isfull: false,
      description: window.localStorage.getItem("article_description") || "No description",
      category_id: window.localStorage.getItem('category_id') || 1,
      slug: window.localStorage.getItem("article_title") ? "<?php echo $_SESSION['username']; ?>_" + window.localStorage.getItem("article_title").toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]+/g, '') + '_' + <?php echo $id; ?> : "<?php echo $_SESSION['username']; ?>_" + "untitled-article_" + <?php echo $id; ?>,
      title: window.localStorage.getItem("article_title") || "Untitled Article",
    }
    run(payload).then(data => {
      console.log(data)
    }).catch(e => {
      console.log(e)
    })
  })
  let run = async (payload) => {
    try {
      const response = await fetch('http://localhost/journal/views/components/api/add.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload),
      });
      let data = await response.json();
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      console.log('Save successful!');
      return data;
    } catch (error) {
      console.error('Save failed:', error);
    }
  }
  titleinput.value = window.localStorage.getItem('article_title')
  // --- Debounce Utility Function ---
  const debounce = (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  };

</script>
<script>
  let editor;
  // -------------------------
  // Auto-Save Function
  // -------------------------
  const saveEditorData = async (api) => {
    try {
      const savedData = await api.saver.save();
      console.log(convertDataToHtml(savedData))
      // console.log('Editor data saved:', html);
      let timerDraft = setTimeout(() => {
        draft.innerHTML = "Saving..."
      }, 2000)
      setTimeout(() => {
        draft.innerHTML = "";
      }, 6000);
      window.localStorage.setItem("article", JSON.stringify(savedData));
      console.log(titleinput.value)
      if (titleinput.value !== "") {
        window.localStorage.setItem("article_title", titleinput.value.trim());
      } else {
        window.localStorage.setItem("article_title", "Untitled Article");
      }
      window.localStorage.setItem("article_id", <?php echo $id; ?>);
      let payload = {
        article_id: <?php echo $id; ?>,
        author_id: <?php echo $_SESSION['user_id']; ?>,
        isfull: true,
        description: window.localStorage.getItem("article_description") || "No description",
        category_id: window.localStorage.getItem('category_id') || 1,
        author_type: "<?php echo $_SESSION['role']; ?>",
        title: window.localStorage.getItem("article_title") || "Untitled Article",
        status: <?php if ($mode == 'draft') {
          echo '"draft"';
        } else {
          echo '"submitted"';
        } ?>,
        slug: window.localStorage.getItem("article_title") ? "<?php echo $_SESSION['username']; ?>_" + window.localStorage.getItem("article_title").toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]+/g, '') + '_' + <?php echo $id; ?> : "<?php echo $_SESSION['username']; ?>_" + "untitled-article_" + <?php echo $id; ?>,
        content_json: savedData,
        content_html: convertDataToHtml(savedData)
      }
      console.log(JSON.stringify(payload))
      // Replace with your actual API endpoint and saving logic
      const response = await fetch('http://localhost/journal/views/components/api/add.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload),
      });
      let data = await response.json();
      console.log(data);


      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      console.log('Save successful!');

    } catch (error) {
      console.error('Save failed:', error);
      // Handle UI feedback for failed save (e.g., show an error message)
    }
  };
  // Create a debounced version of the save function that waits 800ms
  const debouncedSave = debounce(saveEditorData, 800);
  editor = new EditorJS({
    minHeight: 0,
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
      },

    },
    data: {

    },
  });
  async function initEditor() {
    let contentdata = null;

    // --- EDIT ARTICLE ---
    <?php if ($page == 'edit_article') { ?>

      try {
        const article_id = <?php echo $id; ?>;
        const res = await fetch(`http://localhost/journal/views/components/api/get.php?article_id=${article_id}`);
        const data = await res.json();

        if (!res.ok) throw new Error("API error");

        const article = data.article;

        // Save in localStorage
        localStorage.setItem('article', article.content_json);
        localStorage.setItem('article_id', article.article_id);
        localStorage.setItem('article_title', article.title);
        localStorage.setItem('category_id', article.category);
        // Set title input
        titleinput.value = article.title;

        // Parse JSON only once
        contentdata = article.content_json;

      } catch (e) {
        console.error("Error fetching article data:", e);
      }

    <?php } ?>

    // --- ADD ARTICLE ---
    <?php if ($page == "add_article") { ?>
      try {
        const article_id = <?php echo $id; ?>;
        const res = await fetch(`http://localhost/journal/views/components/api/get.php?article_id=${article_id}`);
        const data = await res.json();
        if (!res.ok) {
          if (local && local !== "") {
            contentdata = local; // Important
          }

          document.getElementById("titleinput").value = localStorage.getItem("article_title") || "";
          document.getElementById("category_id").value = localStorage.getItem("category_id") || 1;
        } else {
          const article = data.article;

          // Save in localStorage
          localStorage.setItem('article', article.content_json);
          localStorage.setItem('article_id', article.article_id);
          localStorage.setItem('article_title', article.title);
          localStorage.setItem('category_id', article.category);
          // Set title input
          titleinput.value = article.title;
          document.getElementById("category_id").value = article.category;
          // Parse JSON only once
          contentdata = article.content_json;
        }



      } catch (e) {
        console.error("Error fetching article data:", e);
      }

      let local = localStorage.getItem("article");


    <?php } ?>

    // Render Editor.js
    setTimeout(() => {
      if (contentdata) {
        editor.render(JSON.parse(contentdata))
          .then(() => console.log("Editor.js rendered"))
          .catch(err => console.error("Error rendering:", err));
      }
    }, 300);
  }

  initEditor();





</script>