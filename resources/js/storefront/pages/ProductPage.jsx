import { useEffect, useState } from "react";
import { Link, useParams } from "react-router-dom";
import axios from "axios";
import { FaWhatsapp, FaExternalLinkAlt } from "react-icons/fa";
import { RiArrowLeftSLine } from "react-icons/ri";
import useCartStore from "../store/cartStore";
import useOffcanvasStore from "../store/offcanvasStore";
import useBalanceStore from "../store/balanceStore";
import { WHATSAPP_NUMBER } from "../config";

const ProductPage = () => {
  const { slug } = useParams();
  const [product, setProduct] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const { addToCart } = useCartStore();
  const { toggleOffcanvas } = useOffcanvasStore();
  const { toggleBalanceo } = useBalanceStore();

  useEffect(() => {
    const fetchProduct = async () => {
      try {
        setLoading(true);
        const url = import.meta.env.VITE_PRODUCT_URL || `/api/storefront/products/${slug}`;
        const response = await axios.get(url);
        setProduct(response.data.product);
      } catch (err) {
        setError(err);
      } finally {
        setLoading(false);
      }
    };

    fetchProduct();
  }, [slug]);

  const handleAddToCart = () => {
    if (!product) return;
    addToCart(product);
    toggleBalanceo(true);
    if (!document.querySelector(".offcanvas.show")) {
      toggleOffcanvas();
    }
  };

  const handleWhatsApp = () => {
    const message = `Hola, saludos. Quiero este producto:\n\nProducto: ${product.title}\nPrecio: $${product.price}\n\nSKU: ${product.sku}`;
    window.open(
      `https://api.whatsapp.com/send?phone=${WHATSAPP_NUMBER}&text=${encodeURIComponent(message)}`,
      "_blank",
      "noopener,noreferrer"
    );
  };

  return (
    <div className="container mb-5">
      {loading ? (
          <div className="text-center my-5">
            <div className="spinner-border text-warning" role="status">
              <span className="visually-hidden">Cargando...</span>
            </div>
          </div>
        ) : error || !product ? (
          <div className="text-center my-5">
            <h2 className="text-danger">Producto no encontrado</h2>
            <Link to="/" className="btn btn-dark mt-3">
              Volver al catálogo
            </Link>
          </div>
        ) : (
          <>
            <nav aria-label="breadcrumb" className="mb-4">
              <ol className="breadcrumb">
                <li className="breadcrumb-item">
                  <Link to="/" className="text-decoration-none">
                    <RiArrowLeftSLine className="me-1" />
                    Catálogo
                  </Link>
                </li>
                <li className="breadcrumb-item active fw-bold" aria-current="page">
                  {product.title.length > 40
                    ? `${product.title.slice(0, 40)}...`
                    : product.title}
                </li>
              </ol>
            </nav>

            <div className="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
              <div className="row g-0">
                <div className="col-lg-6 bg-light d-flex align-items-center justify-content-center p-5" style={{ minHeight: "450px" }}>
                  {product.image ? (
                    <img
                      src={product.image}
                      className="img-fluid rounded-3"
                      style={{ maxHeight: "400px", objectFit: "contain" }}
                      alt={product.title}
                    />
                  ) : (
                    <div className="text-center">
                      <i className="fas fa-box-open text-secondary opacity-25" style={{ fontSize: "8rem" }} />
                      <p className="text-muted mt-2">Imagen no disponible</p>
                    </div>
                  )}

                  {product.mlPermalink && (
                    <span
                      className="position-absolute top-0 start-0 m-4 badge rounded-pill px-3 py-2 shadow-sm fs-6"
                      style={{ backgroundColor: "#96B813", color: "#1c1a26" }}
                    >
                      <i className="fas fa-check-circle me-1"></i> Verificado en Mercado Libre
                    </span>
                  )}
                </div>

                <div className="col-lg-6 p-4 p-lg-5 d-flex flex-column justify-content-between">
                  <div>
                    <div className="d-flex justify-content-between align-items-center mb-2">
                      <span className="badge bg-secondary-subtle text-dark border font-monospace px-2 py-1">
                        SKU: {product.sku}
                      </span>
                      {product.inStock ? (
                        <span className="badge bg-success px-3 py-2">
                          <i className="fas fa-check me-1"></i> En Stock ({product.stock})
                        </span>
                      ) : (
                        <span className="badge bg-danger px-3 py-2">Agotado temporalmente</span>
                      )}
                    </div>

                    <h1 className="fw-bold text-dark mb-3">{product.title}</h1>

                    <div className="mb-4">
                      <span className="display-4 fw-bold text-dark">
                        {product.currencyFormat}
                        {product.price}
                      </span>
                      <span className="text-muted ms-1">USD / Precio Oficial</span>
                    </div>

                    {product.style && (
                      <p className="mb-2">
                        <strong>Presentación:</strong> {product.style}
                      </p>
                    )}

                    {product.availableSizes.length > 0 && (
                      <p className="mb-3">
                        <strong>Sizes:</strong> {product.availableSizes.join(" · ")}
                      </p>
                    )}

                    <hr className="my-4 border-secondary opacity-25" />

                    <h6 className="fw-bold text-uppercase text-muted small mb-2">
                      Descripción del Producto
                    </h6>
                    <div className="text-secondary mb-4" style={{ lineHeight: 1.7 }}>
                      {product.description ||
                        "Producto original y garantizado por nuestra tienda oficial. Todo el stock se encuentra sincronizado con nuestra red de distribución en Venezuela."}
                    </div>
                  </div>

                  <div className="d-grid gap-3">
                    <button
                      type="button"
                      className="btn btn-cart btn-lg py-3 fw-bold shadow-sm"
                      onClick={handleAddToCart}
                    >
                      <i className="fas fa-cart-plus me-2"></i> Agregar al carrito
                    </button>

                    <button
                      type="button"
                      className="btn btn-comprar btn-lg py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2"
                      onClick={handleWhatsApp}
                    >
                      <FaWhatsapp /> Comprar por WhatsApp
                    </button>

                    {product.mlPermalink && (
                      <a
                        href={product.mlPermalink}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="btn btn-lg py-3 fw-bold text-decoration-none d-flex align-items-center justify-content-center gap-2"
                        style={{ backgroundColor: "#96B813", color: "#1c1a26" }}
                      >
                        <FaExternalLinkAlt />
                        Comprar a través de <strong>Mercado Libre Venezuela</strong>
                      </a>
                    )}
                  </div>
                </div>
              </div>
            </div>
          </>
        )}
      </div>
  );
};

export default ProductPage;
