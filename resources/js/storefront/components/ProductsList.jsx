import { Link } from "react-router-dom";
import useCartStore from "../store/cartStore";

const ProductsList = ({ products }) => {
  // Usa el store directamente para acceder a addToCart
  const { addToCart } = useCartStore();
  return (
    <div className="container my-5">
      <div className="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        {products.map((product) => (
          <div className="col" key={product.id}>
            <div className="card custom-card h-100">
              <Link
                to={`/product/${product.slug || product.id}`}
                className="text-decoration-none text-dark"
              >
                <div className="position-relative">
                  {product.image ? (
                    <img
                      src={product.image}
                      className="card-img-top"
                      alt={product.title}
                    />
                  ) : (
                    <img
                      src={`/imgs-api/${product.id}.webp`}
                      className="card-img-top"
                      alt={product.title}
                    />
                  )}
                </div>
                <div className="card-body d-flex flex-column">
                  <h5 className="card-title mb-4">{product.title}</h5>
                  <div className="d-flex justify-content-between align-items-center mt-auto">
                    <p className="mb-0">
                      <strong>Precio:</strong> {product.currencyFormat}
                      {product.price}
                    </p>
                  </div>
                </div>
              </Link>
              <div className="card-footer border-0 bg-white pb-3">
                <button
                  className="btn btn-cart w-100"
                  onClick={() => addToCart(product)}
                >
                  Agregar al carrito
                </button>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};

export default ProductsList;
