// const editor = new EditorJS({
//   /**
//    * Id of Element that should contain Editor instance
//    */
//   holder: "editorjs",
//   onChange: (api, event) => {
//             // Your change detection logic here
//             console.log('Editor content changed!', event); 

// },
//   tools: {
//     header: Header,
//     quote: {
//       class: Quote,
//       inlineToolbar: true,
//       shortcut: 'CMD+SHIFT+O',
//       config: {
//         quotePlaceholder: 'Enter a quote',
//         captionPlaceholder: 'Quote\'s author',
//       },
//     },
//      underline: Underline,
//       Marker: {
//       class: Marker,
//       shortcut: 'CMD+SHIFT+M',
//     },
//     List: {
//       class: EditorjsList,
//       inlineToolbar: true,
//       config: {
//         defaultStyle: "unordered",
//       },
//     },
//     raw: RawTool,
//     image: {
//       class: ImageTool,
//       config: {
//         endpoints: {
//           byFile: 'http://localhost/uploadFile', // Your backend file uploader endpoint
//           byUrl: 'http://localhost/fetchUrl', // Your endpoint that provides uploading by Url
//         }
//       }
//     },
//     checklist: {
//       class: Checklist,
//     },
//     linkTool: {
//       class: LinkTool,
//       config: {
//         endpoint:
//           "http://localhost/phppot/jquery/editorjs/extract-link-data.php", // Your backend endpoint for url data fetching,
//       },
//     },
//   },
//   data: {},
// });
// console.log(editor)
// const onSavelocal=(e)=>{
// console.log("hello")
// }