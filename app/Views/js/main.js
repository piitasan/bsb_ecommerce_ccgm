document.addEventListener("DOMContentLoaded", function () {
  const editButtons = document.querySelectorAll(".js-edit-product");
  const editForm = document.getElementById("editProductForm");

  if (!editForm) return;

  const fields = {
    productName: document.getElementById("edit_product_name"),
    categoryId: document.getElementById("edit_category_id"),
    price: document.getElementById("edit_price"),
    stockQty: document.getElementById("edit_stock_qty"),
    shortDescription: document.getElementById("edit_short_description"),
    detailedDescription: document.getElementById("edit_detailed_description"),
  };

  editButtons.forEach((btn) => {
    btn.addEventListener("click", function () {
      const productId = this.dataset.productId;

      editForm.action = `/admin/products/update/${productId}`;
      fields.productName.value = this.dataset.productName || "";
      fields.categoryId.value = this.dataset.categoryId || "";
      fields.price.value = this.dataset.price || "0";
      fields.stockQty.value = this.dataset.stockQty || "0";
      fields.shortDescription.value = this.dataset.shortDescription || "";
      fields.detailedDescription.value = this.dataset.detailedDescription || "";
    });
  });
});