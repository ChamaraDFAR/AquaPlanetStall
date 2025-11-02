// main.js - Exhibition Stall Booking Frontend Logic
// Extracted from index.php for maintainability

// (All JavaScript from the previous <script> block in index.php is now here)

const state = {
  stalls: {},
  pendingStallId: null,
  generatedRef: null,
  confirmCategory: null,
  categories: {}, // id -> {id,name,price}
};

let orgModalEl, orgModal, confirmModalEl, confirmModal, catModalEl, catModal;

function currency(n) {
  return "LKR " + Number(n).toLocaleString();
}
function generateReference() {
  return "BK-" + Math.random().toString(36).substring(2, 9).toUpperCase();
}

function render() {
  renderMap();
  renderSelection();
}

function stallClass(stall) {
  if (stall.status === "booked") return "stall-box stall-booked";
  if (stall.status === "selected")
    return stall.organization === "NAQDA"
      ? "stall-box stall-selected-naqda"
      : "stall-box stall-selected-dfar";
  return "stall-box stall-available";
}

function handleStallClick(id) {
  const s = state.stalls[id];
  if (!s || s.status === "booked") return;
  if (s.status === "available") {
    state.pendingStallId = id;
    document.getElementById("org-stall-id").textContent = id;
    orgModal.show();
  } else if (s.status === "selected") {
    s.status = "available";
    delete s.organization;
    delete s.category_id;
    delete s.category_name;
    render();
  }
}

function selectOrganization(org) {
  const id = state.pendingStallId;
  if (!id) return;
  const s = state.stalls[id];
  if (s) {
    s.status = "selected";
    s.organization = org;
  }
  orgModal.hide();
  const section = id.charAt(0);

  // U-section, P-T section, and V-section all require category selection
  if (
    section === "U" ||
    ["P", "Q", "R", "S", "T"].includes(section) ||
    section === "V"
  ) {
    document.getElementById("cat-stall-id").textContent = id;
    const buttonsContainer = document.getElementById("cat-modal-buttons");
    buttonsContainer.innerHTML = "";

    // Determine which categories to show
    let categoryIds = [];
    if (section === "U") {
      categoryIds = [1, 2]; // U-section: General Restaurant and Special Restaurant
      document.getElementById("cat-modal-title").textContent =
        "Select Restaurant Category";
    } else if (["P", "Q", "R", "S", "T"].includes(section)) {
      categoryIds = [3, 4, 5, 6, 7, 8, 9]; // P-T section categories
      document.getElementById("cat-modal-title").textContent =
        "Select Exhibition Category";
    } else if (section === "V") {
      categoryIds = [10, 11, 12, 13]; // V-section Ornamental Fish Stall categories
      document.getElementById("cat-modal-title").textContent =
        "Select Ornamental Fish Stall Category";
    }

    // Populate buttons dynamically from categories loaded from database
    categoryIds.forEach((catId) => {
      const cat = state.categories[catId];
      if (cat) {
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "btn btn-outline-primary";
        btn.setAttribute("data-cat", catId);
        btn.textContent = `${cat.name} (${currency(cat.price)})`;
        buttonsContainer.appendChild(btn);
      }
    });

    catModal.show();
  } else {
    state.pendingStallId = null;
    render();
  }
}

function selectCategory(catId) {
  const id = state.pendingStallId;
  if (!id) return;
  const s = state.stalls[id];
  if (!s) return;
  const cat = state.categories[catId];
  if (!cat) {
    console.error("Category not found:", catId);
    return;
  }
  s.category_id = cat.id;
  s.category_name = cat.name;
  s.price = Number(cat.price);
  state.pendingStallId = null;
  catModal.hide();
  render();
}

function getByPrefix(prefix) {
  const list = Object.values(state.stalls).filter((s) =>
    s.id.startsWith(prefix)
  );
  list.sort((a, b) => a.id.localeCompare(b.id));
  return list;
}

function createStallButton(stall) {
  const btn = document.createElement("button");
  const isLarge = stall.id.startsWith("U") || stall.id.startsWith("V");
  btn.className = stallClass(stall) + (isLarge ? " stall-large" : "");
  btn.textContent = stall.id;
  btn.disabled = stall.status === "booked";
  btn.addEventListener("click", () => handleStallClick(stall.id));
  return btn;
}

function renderMap() {
  const left = document.getElementById("left-column");
  const right = document.getElementById("right-column");
  left.innerHTML = "";
  right.innerHTML = "";

  // Sections P-Q-R-S-T
  ["P", "Q", "R", "S", "T"].forEach((section) => {
    const wrap = document.createElement("div");
    wrap.className = "d-flex align-items-center gap-3 mb-3";
    const label = document.createElement("div");
    label.className = "section-label";
    label.textContent = section;
    const col = document.createElement("div");
    col.className = "d-flex flex-column gap-2";
    col.style.minWidth = "0";
    col.style.flex = "1";
    const upper = document.createElement("div");
    upper.className = "grid-cols-7";
    const lower = document.createElement("div");
    lower.className = "grid-cols-7";
    const stalls = getByPrefix(section);
    const sortedStalls = stalls.sort(
      (a, b) => parseInt(a.id.substring(1)) - parseInt(b.id.substring(1))
    );
    sortedStalls
      .slice(7, 14)
      .forEach((s) => upper.appendChild(createStallButton(s)));
    sortedStalls
      .slice(0, 7)
      .forEach((s) => lower.appendChild(createStallButton(s)));
    col.appendChild(upper);
    col.appendChild(lower);
    wrap.appendChild(label);
    wrap.appendChild(col);
    left.appendChild(wrap);
  });

  // Food Stalls block (U)
  const foodWrap = document.createElement("div");
  foodWrap.className = "d-flex align-items-start gap-2 mt-3";
  const spacer = document.createElement("div");
  spacer.style.width = "2.5rem";
  spacer.style.minWidth = "2.5rem";
  const area = document.createElement("div");
  area.id = "food-stalls";
  area.className = "bg-light p-3 rounded border";
  area.style.width = "100%";
  area.style.minWidth = "0";
  const h3 = document.createElement("div");
  h3.className = "section-header text-center mb-3";
  h3.textContent = "Food Stalls";
  const uGrid = document.createElement("div");
  uGrid.className = "d-flex flex-column gap-3";
  const firstRow = document.createElement("div");
  firstRow.className = "grid-cols-5";
  const uStalls = getByPrefix("U");
  const sortedUStalls = uStalls.sort(
    (a, b) => parseInt(a.id.substring(1)) - parseInt(b.id.substring(1))
  );
  sortedUStalls
    .slice(15)
    .forEach((s) => firstRow.appendChild(createStallButton(s)));
  const secondRow = document.createElement("div");
  secondRow.className = "grid-cols-15";
  sortedUStalls
    .slice(0, 15)
    .forEach((s) => secondRow.appendChild(createStallButton(s)));
  uGrid.appendChild(firstRow);
  uGrid.appendChild(secondRow);
  area.appendChild(h3);
  area.appendChild(uGrid);
  foodWrap.appendChild(spacer);
  foodWrap.appendChild(area);
  left.appendChild(foodWrap);

  // Right columns (Aquaculture stalls - V)
  const rightColA = document.createElement("div");
  rightColA.className = "d-flex flex-column gap-3";
  rightColA.style.width = "100%";
  rightColA.style.maxWidth = "100%";
  const vHeader = document.createElement("div");
  vHeader.className = "section-header text-center mb-2";
  vHeader.textContent = "AQUACULTURE STALLS";
  rightColA.appendChild(vHeader);
  const vStallsContainer = document.createElement("div");
  vStallsContainer.className = "v-stalls-container";
  const vStalls = getByPrefix("V");
  const sortedStalls = vStalls.sort(
    (a, b) => parseInt(a.id.substring(1)) - parseInt(b.id.substring(1))
  );
  const rows = [
    [76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89],
    [61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75],
    [46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60],
    [31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45],
    [16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30],
    [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
  ];
  rows.forEach((numbers) => {
    const rowDiv = document.createElement("div");
    rowDiv.className = numbers.length === 14 ? "grid-cols-14" : "grid-cols-15";
    numbers.forEach((num) => {
      const stall = sortedStalls.find(
        (s) => parseInt(s.id.substring(1)) === num
      );
      if (stall) {
        rowDiv.appendChild(createStallButton(stall));
      }
    });
    vStallsContainer.appendChild(rowDiv);
  });
  rightColA.appendChild(vStallsContainer);
  const stage = document.createElement("div");
  stage.className = "badge-panel fw-bold text-center mt-2";
  stage.textContent = "MAIN STAGE";
  rightColA.appendChild(stage);
  right.appendChild(rightColA);
}

function renderSelection() {
  const selected = Object.values(state.stalls)
    .filter((s) => s.status === "selected")
    .sort((a, b) => a.id.localeCompare(b.id));
  const list = document.getElementById("selection-list");
  const empty = document.getElementById("selection-empty");
  const summary = document.getElementById("selection-summary");
  const btn = document.getElementById("btn-confirm");
  const totalStalls = document.getElementById("total-stalls");
  const totalPrice = document.getElementById("total-price");
  if (selected.length === 0) {
    list.classList.add("d-none");
    summary.classList.add("d-none");
    empty.classList.remove("d-none");
    btn.disabled = true;
    return;
  }
  empty.classList.add("d-none");
  list.classList.remove("d-none");
  summary.classList.remove("d-none");
  btn.disabled = false;
  list.innerHTML = '<div class="row g-2"></div>';
  const row = list.firstElementChild;
  selected.forEach((s) => {
    const col = document.createElement("div");
    col.className = "col-6";
    const tag = document.createElement("div");
    tag.className =
      "text-center p-2 rounded fw-medium " +
      (s.organization === "DFAR"
        ? "bg-info-subtle text-info-emphasis"
        : "bg-primary-subtle text-primary-emphasis");
    const extra = s.category_name
      ? `<div class=\"small\">${s.category_name}</div>`
      : "";
    tag.innerHTML = `${s.id}<div class=\"small opacity-75\">${s.organization}</div>${extra}`;
    col.appendChild(tag);
    row.appendChild(col);
  });
  const total = selected.reduce((sum, s) => sum + Number(s.price || 0), 0);
  totalStalls.textContent = String(selected.length);
  totalPrice.textContent = currency(total);
}

function openConfirm() {
  const selected = Object.values(state.stalls).filter(
    (s) => s.status === "selected"
  );
  if (selected.length === 0) return;
  state.generatedRef = generateReference();
  document.getElementById("ref-number").textContent = state.generatedRef;
  state.confirmCategory = "Other";
  const list = document.getElementById("confirm-list");
  list.innerHTML = "";
  selected
    .sort((a, b) => a.id.localeCompare(b.id))
    .forEach((s) => {
      const row = document.createElement("div");
      row.className = "d-flex justify-content-between border-bottom py-1 small";
      const label = s.category_name ? `, ${s.category_name}` : "";
      row.innerHTML = `<span>Stall <strong>${s.id}</strong> (${
        s.organization
      }${label})</span><span class=\"fw-bold\">${currency(s.price)}</span>`;
      list.appendChild(row);
    });
  const total = selected.reduce((sum, s) => sum + Number(s.price || 0), 0);
  document.getElementById("confirm-total").textContent = currency(total);
  confirmModal.show();
}

async function proceedBooking() {
  const selected = Object.values(state.stalls).filter(
    (s) => s.status === "selected"
  );
  const payloadStalls = selected.map((s) => ({
    id: s.id,
    organization: s.organization,
    category_id: s.category_id,
  }));
  const total = selected.reduce((sum, s) => sum + Number(s.price || 0), 0);
  const hasVStalls = selected.some((s) => s.id.startsWith("V"));
  const hasPaymentStalls = selected.some((s) => {
    const section = s.id.charAt(0);
    return section === "U" || ["P", "Q", "R", "S", "T"].includes(section);
  });
  if (!state.confirmCategory) {
    state.confirmCategory = "Other";
  }
  try {
    const res = await fetch("./api/book.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        stalls: payloadStalls,
        totalPrice: total,
        category: state.confirmCategory,
      }),
    });
    const data = await res.json();
    if (res.ok && data && data.ok && data.reference) {
      const ref = data.reference;
      selected.forEach((s) => {
        const tgt = state.stalls[s.id];
        if (tgt) {
          tgt.status = "booked";
          tgt.booking_ref = ref;
        }
      });
      render();
      let target;
      if (hasVStalls) {
        target = "gov_payment.php?ref=" + encodeURIComponent(ref);
      } else if (hasPaymentStalls) {
        target =
          "other.php?ref=" + encodeURIComponent(ref) + "&message=payment";
      } else {
        target =
          state.confirmCategory === "Ornamental"
            ? "ornamental.php"
            : "other.php";
        target = `${target}?ref=${encodeURIComponent(ref)}`;
      }
      window.location.assign(target);
    } else {
      let errorMsg =
        data && data.error ? data.error : "Booking failed. Please try again.";
      alert(errorMsg);
    }
  } catch (e) {
    alert("Booking failed. Please try again.");
  }
}

async function loadStalls() {
  const buildLocal = () => {
    const map = {};
    const standard = 150,
      premium = 250;
    ["P", "Q", "R", "S", "T"].forEach((sec) => {
      for (let i = 1; i <= 14; i++) {
        map[sec + i] = { id: sec + i, status: "available", price: standard };
      }
    });
    for (let i = 1; i <= 15; i++) {
      map["U" + i] = { id: "U" + i, status: "available", price: 400000 };
    }
    for (let i = 16; i <= 20; i++) {
      map["U" + i] = { id: "U" + i, status: "available", price: 200000 };
    }
    for (let i = 1; i <= 89; i++) {
      map["V" + i] = {
        id: "V" + i,
        status: "available",
        price: i > 75 ? premium : standard,
      };
    }
    state.stalls = map;
  };
  try {
    const [resStalls, resCats] = await Promise.all([
      fetch("./api/stalls.php"),
      fetch("./api/categories.php"),
    ]);
    const data = await resStalls.json();
    const cats = await resCats.json();
    if (cats && Array.isArray(cats.categories)) {
      const cmap = {};
      cats.categories.forEach((c) => {
        cmap[c.id] = {
          id: Number(c.id),
          name: String(c.name),
          price: Number(c.price),
        };
      });
      state.categories = cmap;
    } else {
      state.categories = {
        1: { id: 1, name: "General Restaurant", price: 200000 },
        2: { id: 2, name: "Special Restaurant", price: 400000 },
        3: { id: 3, name: "Banking partner", price: 3500000 },
        4: { id: 4, name: "Platinum sponsor", price: 3200000 },
        5: { id: 5, name: "Gold sponsor", price: 3000000 },
        6: { id: 6, name: "Silver sponsor", price: 2500000 },
        7: { id: 7, name: "Bronze sponsor", price: 2000000 },
        8: { id: 8, name: "Co sponsor stalls", price: 1500000 },
        9: { id: 9, name: "General Exhibition stall", price: 200000 },
        10: { id: 10, name: "Ornamental Fish Stall(A)", price: 500000 },
        11: { id: 11, name: "Ornamental Fish Stall(B)", price: 400000 },
        12: { id: 12, name: "Ornamental Fish Stall(C)", price: 300000 },
        13: { id: 13, name: "Ornamental Fish Stall(D)", price: 200000 },
      };
    }
    if (data && Array.isArray(data.stalls) && data.stalls.length > 0) {
      const map = {};
      data.stalls.forEach((s) => {
        let categoryId = s.category_id ? Number(s.category_id) : undefined;
        let categoryName = categoryId
          ? state.categories[categoryId]?.name
          : undefined;
        let price = Number(s.price);
        if (categoryId && state.categories[categoryId]) {
          price = Number(state.categories[categoryId].price);
        }
        map[s.id] = {
          id: s.id,
          status: s.status,
          price: price,
          organization: s.organization,
          booking_ref: s.booking_ref,
          category_id: categoryId,
          category_name: categoryName,
        };
      });
      state.stalls = map;
    } else {
      buildLocal();
    }
  } catch (_) {
    state.categories = {
      1: { id: 1, name: "General Restaurant", price: 200000 },
      2: { id: 2, name: "Special Restaurant", price: 400000 },
      3: { id: 3, name: "Banking partner", price: 3500000 },
      4: { id: 4, name: "Platinum sponsor", price: 3200000 },
      5: { id: 5, name: "Gold sponsor", price: 3000000 },
      6: { id: 6, name: "Silver sponsor", price: 2500000 },
      7: { id: 7, name: "Bronze sponsor", price: 2000000 },
      8: { id: 8, name: "Co sponsor stalls", price: 1500000 },
      9: { id: 9, name: "General Exhibition stall", price: 200000 },
      10: { id: 10, name: "Ornamental Fish Stall(A)", price: 500000 },
      11: { id: 11, name: "Ornamental Fish Stall(B)", price: 400000 },
      12: { id: 12, name: "Ornamental Fish Stall(C)", price: 300000 },
      13: { id: 13, name: "Ornamental Fish Stall(D)", price: 200000 },
    };
    buildLocal();
  }
  render();
}

document.addEventListener("DOMContentLoaded", () => {
  orgModalEl = document.getElementById("orgModal");
  orgModal = new bootstrap.Modal(orgModalEl);
  confirmModalEl = document.getElementById("confirmModal");
  confirmModal = new bootstrap.Modal(confirmModalEl);

  document.getElementById("btn-confirm").addEventListener("click", openConfirm);
  document
    .getElementById("btn-proceed")
    .addEventListener("click", proceedBooking);
  document.getElementById("btn-copy-ref").addEventListener("click", () => {
    const ref = document.getElementById("ref-number").textContent || "";
    navigator.clipboard.writeText(ref);
  });
  orgModalEl.addEventListener("click", (e) => {
    const target = e.target.closest("button[data-org]");
    if (!target) return;
    const org = target.getAttribute("data-org");
    selectOrganization(org);
  });

  // Category modal events
  catModalEl = document.getElementById("catModal");
  catModal = new bootstrap.Modal(catModalEl);
  catModalEl.addEventListener("click", (e) => {
    const target = e.target.closest("button[data-cat]");
    if (!target) return;
    const cat = Number(target.getAttribute("data-cat"));
    selectCategory(cat);
  });

  loadStalls();
});
