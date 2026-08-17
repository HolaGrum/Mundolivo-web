import { useState } from "react";
import axios from "axios";
import { RiDeleteBin6Line } from "react-icons/ri";
import { FaWhatsapp } from "react-icons/fa";
import useCartStore from "../store/cartStore";
import useOffcanvasStore from "../store/offcanvasStore";
import { WHATSAPP_NUMBER } from "../config";

const SidebarOffCanvas = () => {
  // Acceder al store de cart y usar sus funciones
  const { cart, removeFromCart } = useCartStore();
  const { isVisible, toggleOffcanvas } = useOffcanvasStore();

  const [customerName, setCustomerName] = useState("");
  const [customerPhone, setCustomerPhone] = useState("");
  const [submitting, setSubmitting] = useState(false);
  const [submitError, setSubmitError] = useState(null);

  const calculateSubtotal = () => {
    return cart.reduce((acc, p) => acc + p.price * p.quantity, 0);
  };

  const generateWhatsAppMessage = () => {
    let message = "Hola, saludos. Quiero este pedido:\n\n";
    cart.forEach((product) => {
      message += `Producto: ${product.title}\nCantidad: ${product.quantity}\nPrecio: $${product.price}\n\n`;
    });
    message += `Subtotal: $${calculateSubtotal().toFixed(2)}`;
    if (customerName.trim()) {
      message += `\n\nNombre: ${customerName.trim()}`;
    }
    if (customerPhone.trim()) {
      message += `\nTeléfono: ${customerPhone.trim()}`;
    }
    return encodeURIComponent(message);
  };

  const handleCheckout = async () => {
    setSubmitting(true);
    setSubmitError(null);

    try {
      // Registra la orden en Vanilo (backend) para que aparezca en el backoffice
      await axios.post("/api/storefront/orders", {
        items: cart.map((product) => ({
          id: product.id,
          quantity: product.quantity,
        })),
        customer: {
          name: customerName.trim(),
          phone: customerPhone.trim(),
          message: "Pedido enviado desde la tienda web por WhatsApp.",
        },
      });
    } catch (error) {
      setSubmitError("No se pudo guardar el pedido en el sistema, pero igualmente puedes enviarlo por WhatsApp.");
    } finally {
      setSubmitting(false);
    }

    // Abre WhatsApp con el mensaje del pedido (flujo de confirmación con el vendedor)
    window.open(
      `https://api.whatsapp.com/send?phone=${WHATSAPP_NUMBER}&text=${generateWhatsAppMessage()}`,
      "_blank",
      "noopener,noreferrer"
    );
  };

  return (
    <div
      className={`offcanvas offcanvas-end px-1 ${
        isVisible ? "show offcanvas-open" : ""
      }`}
      tabIndex="-1"
      id="offcanvasRight"
      aria-labelledby="offcanvasRightLabel"
    >
      <div className="offcanvas-header">
        <h5
          className="offcanvas-title text-uppercase text-center fw-bold"
          id="offcanvasRightLabel"
        >
          Mi carrito de compras
        </h5>
        <button
          type="button"
          className="btn-close"
          onClick={toggleOffcanvas}
          aria-label="Close"
        ></button>
      </div>

      <div className="offcanvas-body">
        {cart.length === 0 ? (
          <p className="text-center mt-5">No hay productos en el carrito.</p>
        ) : (
          cart.map((productCart) => (
            <div
              className="row align-items-center mb-2 py-1"
              style={{ borderBottom: "1px dashed rgb(176, 176, 176)" }}
              key={productCart.id}
            >
              <div className="col-3">
                {productCart.image ? (
                  <img
                    src={productCart.image}
                    className="card-img-top border-radius-5"
                    alt={productCart.title}
                  />
                ) : (
                  <img
                    src={`/imgs-api/${productCart.id}.webp`}
                    className="card-img-top border-radius-5"
                    alt={productCart.title}
                  />
                )}
              </div>
              <div className="col-6">
                <h4 className="mb-4 title-product">{productCart.title}</h4>
                <p className="mb-0 detalles-product">
                  {productCart.description}
                </p>
              </div>
              <div className="col-3 text-end">
                <span className="fw-bold">
                  <span className="fs-6 color-gris">
                    {productCart.quantity}x
                  </span>
                  <strong className="fs-5 precio">${productCart.price}</strong>
                </span>
                <button
                  className="btn mt-3 delete-product"
                  onClick={() => removeFromCart(productCart.id)}
                >
                  <RiDeleteBin6Line />
                </button>
              </div>
            </div>
          ))
        )}
      </div>

      <div className="offcanvas-footer mt-4 px-2">
        <div className="d-flex justify-content-between align-items-center">
          <h5 className="mb-5">
            <span className="fw-bold">SUBTOTAL:</span>
              <span className="fw-bold float-end px-2 fs-2">
              <span style={{ color: "#96B813" }}>$</span>
              {calculateSubtotal().toFixed(2)}
            </span>
          </h5>
        </div>

        {cart.length > 0 && (
          <>
            <div className="mb-3">
              <input
                type="text"
                className="form-control mb-2"
                placeholder="Tu nombre (opcional)"
                value={customerName}
                onChange={(e) => setCustomerName(e.target.value)}
              />
              <input
                type="tel"
                className="form-control"
                placeholder="Tu teléfono (opcional)"
                value={customerPhone}
                onChange={(e) => setCustomerPhone(e.target.value)}
              />
            </div>

            {submitError && (
              <div className="alert alert-warning small py-2 mb-2">{submitError}</div>
            )}

            <button
              className="btn btn-comprar w-100 d-flex justify-content-center align-items-center"
              onClick={handleCheckout}
              disabled={submitting}
            >
              {submitting ? (
                <>
                  <span
                    className="spinner-border spinner-border-sm me-2"
                    role="status"
                  ></span>
                  Registrando pedido...
                </>
              ) : (
                <>
                  <FaWhatsapp /> &nbsp; Enviar pedido por WhatsApp
                </>
              )}
            </button>
          </>
        )}
      </div>
    </div>
  );
};

export default SidebarOffCanvas;
