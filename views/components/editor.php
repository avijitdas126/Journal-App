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
?>
<div class="container py-3">
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
      <div class="editor-scroll-box p-3 bg-white shadow-sm rounded">
        <div id="editorjs"></div>
      </div>
    </div>
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
  const draft = document.querySelector('#badged')
  const titleinput = document.querySelector('#titleinput')
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
      slug: window.localStorage.getItem("article_title") ? "<?php echo $_SESSION['username']; ?>_" + window.localStorage.getItem("article_title").toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]+/g, '') + '_' + <?php echo $id; ?> : "<?php echo $_SESSION['username']; ?>_" + "untitled-article_" + <?php echo $id; ?>,
      title: window.localStorage.getItem("article_title") || "Untitled Article",
    }
    run(payload).then(data=>{
     console.log(data)
    }).catch(e=>{
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
      category_id: window.localStorage.getItem('category_id') || 0,
      slug: window.localStorage.getItem("article_title") ? "<?php echo $_SESSION['username']; ?>_" + window.localStorage.getItem("article_title").toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]+/g, '') + '_' + <?php echo $id; ?> : "<?php echo $_SESSION['username']; ?>_" + "untitled-article_" + <?php echo $id; ?>,
      title: window.localStorage.getItem("article_title") || "Untitled Article",
    }
    run(payload).then(data=>{
     console.log(data)
    }).catch(e=>{
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
        category_id: window.localStorage.getItem('category_id') || 0,
        author_type: "<?php echo $_SESSION['role']; ?>",
        title: window.localStorage.getItem("article_title") || "Untitled Article",
        status: "draft",
        slug: window.localStorage.getItem("article_title") ? "<?php echo $_SESSION['username']; ?>_" + window.localStorage.getItem("article_title").toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]+/g, '') + '_' + <?php echo $id; ?> : "<?php echo $_SESSION['username']; ?>_" + "untitled-article_" + <?php echo $id; ?>,
        content_json: JSON.stringify(savedData),
        content_html: convertDataToHtml(savedData)
      }
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
    let contentData = null;
    // --- EDIT ARTICLE ---
    <?php if ($page == 'edit_article') { ?>
      let fetchedData = () => {
        return new Promise(async (resolve, reject) => {
          try {
            const article_id = <?php echo $id; ?>;
            const response = await fetch(`http://localhost/journal/views/components/api/get.php?article_id=${article_id}`);
            const data = await response.json();
            if (response.ok) {
              resolve(data.article);
            } else {
              reject('Failed to fetch article data');
            }
          } catch (error) {
            reject(error);
          }
        });
      };
      fetchedData().then(article => {
        window.localStorage.setItem('article', article.content_json);
        window.localStorage.setItem('article_id', article.article_id);
        window.localStorage.setItem('article_title', article.title);
        titleinput.value = article.title;
        contentdata = article.content_json;
      }).catch(error => {
        console.error('Error fetching article data:', error);
      });
    <?php } ?>
    // --- ADD ARTICLE ---
    <?php if ($page == "add_article") { ?>
      contentData = localStorage.getItem("article") || {};
      document.getElementById("titleinput").value = localStorage.getItem("article_title") || "";
    <?php } ?>
    setTimeout(() => {
      if (contentdata) {
        editor.render(JSON.parse(contentdata)).then(() => {
          console.log("Editor.js has been rendered");
        }).catch((error) => {
          console.error("Error rendering Editor.js:", error);
        });
      }
    }, 1000);
  }

  initEditor();




</script>