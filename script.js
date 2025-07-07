$(function () {
  // 安全にHTMLエスケープする関数
  function escapeHtml(text) {
    return $('<div>').text(String(text)).html();
  }

  // --- 検索処理 ---
  $("#search-btn").on("click", function () {
    const keyword = $("#keyword").val().trim();
    if (!keyword) {
      $("#results").text("キーワードを入力してください");
      return;
    }
    $.get("./menu_search.php", { keyword }, function (res) {
      if (res.status === "success") {
        if (res.data.length === 0) {
          $("#results").text("該当するメニューはありません");
          return;
        }
        let html = "<ul>";
        res.data.forEach(function (menu) {
          html += `<li>${escapeHtml(menu.name)} - ¥${escapeHtml(menu.price)}</li>`;
        });
        html += "</ul>";
        $("#results").html(html);
      } else {
        $("#results").text("検索失敗：" + escapeHtml(res.message));
      }
    }, "json").fail(function () {
      $("#results").text("通信エラーが発生しました。");
    });
  });

  // --- レビュー投稿フォーム処理 ---
  $("#review-form").on("submit", function(e){
    e.preventDefault();
    const name = $("#name").val().trim();
    const comment = $("#comment").val().trim();
    if(!name || !comment){
      $("#review-message").text("名前とコメントを入力してください。");
      return;
    }
    $.post("./review_post.php", { name, comment }, function(res){
      if(res.status === "success"){
        $("#review-message").text("レビューを投稿しました！ありがとうございます。");
        $("#review-form")[0].reset();
        loadReviews();
      } else {
        $("#review-message").text("投稿に失敗しました：" + res.message);
      }
    }, "json").fail(function(){
      $("#review-message").text("通信エラーが発生しました。");
    });
  });

  // --- レビュー一覧読み込み ---
function loadReviews() {
  // 1. 表示を一旦リセット
  $("#review-list").html("読み込み中...");

  // 2. PHPにデータ取得リクエスト
  $.getJSON("./review_list.php", function(res){
    // 3. 成功時の処理
    if (res.status === "success") {
      // データが空なら「まだレビューはありません」と表示
      if (res.data.length === 0) {
        $("#review-list").html("<p>まだレビューはありません。</p>");
        return;
      }

      // 4. HTML組み立て
      let html = "";
      res.data.forEach(function(review) {
        html += `
          <div class="p-4 border rounded bg-white" data-id="${review.id}">
            <p class="font-bold">${escapeHtml(review.name)}</p>
            <p class="comment">${escapeHtml(review.comment)}</p>
            <p class="text-sm text-gray-500">${escapeHtml(review.indate)}</p>
            <button class="edit-btn text-blue-600 mr-2">編集</button>
            <button class="delete-btn text-red-600">削除</button>
          </div>
        `;
      });

      // 5. 表示領域に挿入
      $("#review-list").html(html);
    } else {
      $("#review-list").html("<p>レビューの読み込みに失敗しました。</p>");
    }
  }).fail(function(){
    // 通信エラー
    $("#review-list").html("<p>通信エラーが発生しました。</p>");
  });
}


  // --- 編集処理 ---
  $(document).on("click", ".edit-btn", function() {
    const parent = $(this).closest("div[data-id]");
    const id = parent.data("id");
    const oldComment = parent.find(".comment").text();
    const newComment = prompt("コメントを編集してください：", oldComment);
    if (newComment && newComment !== oldComment) {
      $.post("review_update.php", { id: id, comment: newComment }, function(res) {
        if (res.status === "success") {
          loadReviews();
        } else {
          alert("更新失敗：" + res.message);
        }
      }, "json");
    }
  });

  // --- 削除処理 ---
  $(document).on("click", ".delete-btn", function() {
    const parent = $(this).closest("div[data-id]");
    const id = parent.data("id");
    if (confirm("本当に削除しますか？")) {
      $.post("review_delete.php", { id: id }, function(res) {
        if (res.status === "success") {
          loadReviews();
        } else {
          alert("削除失敗：" + res.message);
        }
      }, "json");
    }
  });

  // --- タブ切り替え処理 ---
  $(".tab").on("click", function(){
    const selected = $(this).data("tab");
    $(".tab-content").addClass("hidden");
    $("#" + selected).removeClass("hidden");
    $(".tab").removeClass("bg-green-300").addClass("bg-green-200");
    $(this).removeClass("bg-green-200").addClass("bg-green-300");

    if(selected === "review"){
      loadReviews();
    }
  });
});
