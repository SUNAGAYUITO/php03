  function escapeHtml(text) {
    return $('<div>').text(String(text)).html();
  }

$(function () {
  // 安全にHTMLエスケープする関数

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

      let html = '<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">';
      res.data.forEach(function (menu) {
        html += `
          <div class="bg-white p-4 rounded-2xl shadow-md hover:shadow-lg transform hover:scale-105 transition duration-300">
            <img src="${escapeHtml(menu.image_path)}" alt="${escapeHtml(menu.name)}" class="w-full h-48 object-cover rounded-xl border border-green-300 mb-3">
            <h3 class="text-xl font-bold text-green-700">${escapeHtml(menu.name)}</h3>
            <p class="text-gray-600 text-sm mt-1">価格：¥${escapeHtml(menu.price)}</p>
          </div>
        `;
      });
      html += '</div>';

      $("#results").html(html);
    } else {
      $("#results").text("検索失敗：" + escapeHtml(res.message));
    }
  }, "json").fail(function () {
    $("#results").text("通信エラーが発生しました。");
  });
});



  // --- レビュー投稿フォーム処理 ---
 // --- レビュー投稿フォーム処理 ---
$("#review-form").on("submit", function(e){
  e.preventDefault();

  const formData = new FormData(this); // すべての入力を自動取得

  $.ajax({
    url: "./review_post.php",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function(res){
      if(res.status === "success"){
        $("#review-message").text(res.message || "レビューを投稿しました！");
        $("#review-form")[0].reset();
        loadReviews();
      } else {
        $("#review-message").text("投稿に失敗：" + res.message);
      }
    },
    error: function(){
      $("#review-message").text("通信エラーが発生しました。");
    }
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
    <p class="font-bold">${escapeHtml(review.menu_name)} 評価：${"★".repeat(review.rating)}（${review.rating}）</p>
    <p class="text-sm text-gray-600">投稿者：${escapeHtml(review.name)}</p>
    <p class="comment">${escapeHtml(review.comment)}</p>
    ${review.image_path ? `<img src="${escapeHtml(review.image_path)}" class="w-32 h-auto mt-2">` : ''}
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
    if (selected === "ranking") {
  loadRanking();
  loadRecommendation(); // ←追加
}
if (selected === "point") {
  loadPointHistory();  // ←これを追加
}

  });
});




function loadRanking() {
  $("#ranking-list").html("読み込み中...");

  $.getJSON("ranking.php", function(res) {
    if (res.status === "success") {
      if (res.data.length === 0) {
        $("#ranking-list").html("<li>ランキングデータがありません。</li>");
        return;
      }

      let html = "";
      res.data.forEach(function(item, index) {
      html += `<li class="bg-white p-4 rounded shadow flex items-center space-x-4">
  ${item.image_path ? `<img src="${escapeHtml(item.image_path)}" class="w-20 h-20 object-cover rounded">` : ''}
  <div>
    <div class="font-bold">${index + 1}位：${escapeHtml(item.menu_name)}</div>
    <div class="text-yellow-500">★${item.avg_rating} <span class="text-gray-600">（${item.review_count}件）</span></div>
  </div>
</li>`;
      });

      $("#ranking-list").html(html);
    } else {
      $("#ranking-list").html("ランキング取得失敗：" + escapeHtml(res.message));
    }
  }).fail(function() {
    $("#ranking-list").html("通信エラーが発生しました。");
  });
}


// function loadReviewSummary() {
//   $.getJSON("summarize.php", function(res) {
//     if (res.status === "success") {
//       $("#review-summary").html(`<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 text-yellow-800">
//         <strong>AI要約：</strong> ${escapeHtml(res.summary)}
//       </div>`);
//     } else {
//       $("#review-summary").text("要約の取得に失敗しました。");
//     }
//   });
// }

function loadRecommendation() {
  $("#ai-recommend").text("AIがおすすめを選定中...");

  $.getJSON("recommend.php", function(res) {
    if (res.status === "success") {
      $("#ai-recommend").html(`
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 text-blue-800">
          <strong>🎓 迷っているあなたへ！</strong><br>
          ${escapeHtml(res.recommendation)}
        </div>
      `);
    } else {
      $("#ai-recommend").text("おすすめメニューの取得に失敗しました。");
    }
  });
}

