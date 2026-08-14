import Typewriter from "typewriter-effect";
import imgShopping from "../assets/imgs/shopping.png";

const TitleTypeWriter = () => {
  return (
    <section className="row align-items-center">
      <div className="col-12 col-md-7">
        <h1 className="display-5 titulo">
          Tienda de <span style={{ color: "#96B813" }}>Pinturas</span> y
          accesorios para ferretería
        </h1>
        <h3 className="text-center">
          <Typewriter
            options={{
              strings: [
                "Pinturas de calidad para interiores y exteriores",
                "Brochas, rodillos y accesorios profesionales",
                "Envío rápido y asesoría técnica"
              ],
              autoStart: true,
              loop: true,
              deleteSpeed: 50,
              delay: 75,
            }}
          />
        </h3>
      </div>
      <div className="col-12 col-md-5 text-center">
        <img
          style={{ width: "350px", maxWidth: "100%" }}
          src={imgShopping}
          alt="Pinturas y accesorios"
          className="img-fluid text-center px-3"
        />
      </div>
    </section>
  );
};

export default TitleTypeWriter;
