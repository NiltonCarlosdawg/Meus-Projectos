const Hero = () => {
  return (
    <div>
      <div className=" absolute z-[1] left-20">
        <h1 className="text-[55px] font-extrabold text-white font-[popbold] w-[54%] z-[1] ">Festival de Provedores de internet 2025</h1>
        <button className="bg-[#391cb2] py-3 px-16 text-white font-bold text-[13px] hover:scale-[1.2] transition-all">Saiba mais</button>

      </div>
      <img src="festi (1).jpg" alt="" className="w-[40%] absolute top-[200px] left-[250px]"/>
      <img src="festi (3).jpg" alt="" className="absolute top-0 w-[25%] right-0 opacity-80" />
    </div>
  )
}

export default Hero