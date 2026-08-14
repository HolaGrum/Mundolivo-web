import {
  FaYoutube,
  FaLinkedin,
  FaNpm,
  FaChrome,
  FaPaypal,
  FaInstagram,
  FaWhatsapp,
} from "react-icons/fa";
import mercadolibreLogo from "../assets/imgs/mercado-libre-seeklogo.png";

const Footer = () => {
  return (
    <footer id="contact" className="footer bg-dark text-white mt-5 py-4">
      <div className="container">
        <div className="row align-items-center">
          <div className="col-12 col-md-6 mb-3 mb-md-0 text-center text-md-start">
            <h6 className="mb-2">Contacto</h6>
            <p className="mb-0">
              Email: <a href="mailto:inversionesmundolivos@gmail.com" className="text-white">inversionesmundolivos@gmail.com</a>
            </p>
            <p className="mb-0">Tel: +58 424-9322531</p>
          </div>

          <div className="col-12 col-md-6 text-center text-md-end">
            <h6 className="mb-2">Síguenos</h6>
            <div className="social-icons d-inline-flex align-items-center">
              <a href="https://www.instagram.com/mundolivospzo/?hl=es-la" target="_blank" rel="noopener noreferrer" className="text-white me-3" aria-label="Instagram">
                <FaInstagram />
              </a>
              <a href="https://wa.me/584249322531" target="_blank" rel="noopener noreferrer" className="text-white me-3" aria-label="WhatsApp">
                <FaWhatsapp />
              </a>
              <a href="https://www.mercadolibre.com" target="_blank" rel="noopener noreferrer" className="text-white" aria-label="MercadoLibre">
                <img src={mercadolibreLogo} alt="MercadoLibre" style={{ width: "28px" }} />
              </a>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
