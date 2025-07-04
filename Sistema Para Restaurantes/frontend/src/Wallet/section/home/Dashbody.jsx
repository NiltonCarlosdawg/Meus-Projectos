import Listen from "./Listen"

const Dashbody = () => {
  return (
    <div className="flex items-center justify-around overflow-y-auto mt-[40px] px-[2%]">
      <Listen/>
      <img src="/hero.svg" alt="ee" />
      <div>
        <h1 className="text-[30px] w-[500px] font-[quick] font-bold">Trade onde tu vais, em qualquer lugar, em qualquer momento.</h1>
        <div className="mt-[60px] flex items-center gap-[40px]">
          <img src="/barras.png" alt="" className="w-[150px] rounded-[5px]" />
          <div>
            <p className="text-[gray]">Scan to download the app</p>
            <span className="text-[25px]">IOS and Android</span>
          </div>
        </div>
      </div>
    </div>
  )
}

export default Dashbody