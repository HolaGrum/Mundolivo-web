import React from "react";

const CategoryFilter = ({ categories = [], selected, onSelect }) => {
  return (
    <div className="mb-4">
      <h5 className="fw-bold">Filtrar por categoría</h5>
      {categories.map((cat) => (
        <div key={cat.name} className="mb-3">
          <div className="fw-semibold">{cat.name}</div>
          <div className="d-flex flex-wrap gap-2 mt-2">
            {cat.subcategories.map((sub) => (
              <button
                key={sub}
                className={`btn btn-sm btn-outline-secondary ${selected === sub ? "active" : ""}`}
                onClick={() => onSelect(selected === sub ? null : sub)}
              >
                {sub}
              </button>
            ))}
          </div>
        </div>
      ))}
    </div>
  );
};

export default CategoryFilter;
