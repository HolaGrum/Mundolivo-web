import Typewriter from "typewriter-effect";

const TitleTypeWriter = () => {
  return (
    <section className="row align-items-center">
      <div className="col-12">
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
    </section>
  );
};

export default TitleTypeWriter;
