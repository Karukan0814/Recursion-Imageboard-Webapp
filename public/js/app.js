document.addEventListener("DOMContentLoaded", function () {
  // フォームを選択します
  const form = document.getElementById("update-part-form");

  form.addEventListener("submit", function (event) {
    // デフォルトのフォーム送信を防止します
    event.preventDefault();

    // FormDataオブジェクトを作成し、コンストラクタにフォームを渡してすべての入力値を取得します
    const formData = new FormData(form);

    // fetchリクエストを送信します
    fetch("/form/update/part", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json()) // レスポンスからJSONを解析します
      .then((data) => {
        // サーバからのレスポンスデータを処理します
        if (data.status === "success") {
          // 成功メッセージを表示したり、リダイレクトしたり、コンソールにログを出力する可能性があります
          console.log(data.message);
          alert("Update successful!");
          if (!formData.has("id")) form.reset();
        } else if (data.status === "error") {
          // ユーザーにエラーメッセージを表示します
          console.error(data.message);
          alert("Update failed: " + data.message);
        }
      })
      .catch((error) => {
        // ネットワークエラーかJSONの解析エラー
        console.error("Error:", error);
        alert("An error occurred. Please try again.");
      });
  });
});
